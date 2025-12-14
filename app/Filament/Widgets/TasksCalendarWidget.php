<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Carbon\Carbon;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TasksCalendarWidget extends FullCalendarWidget
{
    public string | array | null $fetchEventsFrom = 'getEvents';

    public function getHeading(): ?string
    {
        return 'Lịch công việc';
    }

    public function getEvents(): array
    {
        $user = auth()->user();
        
        $query = Task::query()
            ->whereNotNull('due_date');
        
        // Filter theo phân quyền
        if ($user) {
            // Chủ tịch và Ban điều hành xem tất cả
            if (!$user->hasRole('chu-tich') && !$user->hasRole('ban-dieu-hanh')) {
                // Giám đốc DA xem công việc của dự án thuộc phòng ban mình
                if ($user->hasRole('giam-doc-da')) {
                    $query->whereHas('project', function($q) use ($user) {
                        $q->where('department_id', $user->department_id);
                    })->orWhereNull('project_id');
                }
                // Trưởng phòng xem công việc của phòng ban mình
                elseif ($user->hasRole('truong-phong')) {
                    $query->where(function($q) use ($user) {
                        $q->whereHas('assignees', function($subQ) use ($user) {
                            $subQ->whereHas('department', function($deptQ) use ($user) {
                                $deptQ->where('id', $user->department_id);
                            });
                        })
                        ->orWhereHas('project', function($subQ) use ($user) {
                            $subQ->where('department_id', $user->department_id);
                        })
                        ->orWhereHas('creator', function($subQ) use ($user) {
                            $subQ->where('department_id', $user->department_id);
                        });
                    });
                }
                // Nhân viên chỉ xem công việc được giao cho mình hoặc mình tạo
                elseif ($user->hasRole('nhan-vien')) {
                    $query->where(function($q) use ($user) {
                        $q->whereHas('assignees', function($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        })->orWhere('creator_id', $user->id);
                    });
                }
            }
        }
        
        return $query->get()
            ->map(function (Task $task) {
                $color = match ($task->priority) {
                    'urgent' => '#ef4444',    // Đỏ - Khẩn
                    'high' => '#f59e0b',       // Cam - Cao
                    'medium' => '#3b82f6',     // Xanh - Trung bình
                    'low' => '#6b7280',        // Xám - Thấp
                    default => '#6b7280',
                };

                // Lấy danh sách người được giao
                $assignees = $task->assignees->pluck('name')->join(', ');
                if (empty($assignees) && $task->assignee) {
                    $assignees = $task->assignee->name; // Fallback cho assignee cũ
                }

                return [
                    'id' => $task->id,
                    'title' => $task->title . ($assignees ? ' (' . $assignees . ')' : ''),
                    'start' => $task->due_date->format('Y-m-d'),
                    'end' => $task->due_date->format('Y-m-d'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'task_id' => $task->id,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'assignees' => $assignees,
                        'project' => $task->project?->name,
                    ],
                ];
            })
            ->toArray();
    }

    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'locale' => 'vi',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'editable' => true, // Cho phép kéo thả
            'selectable' => true,
            'droppable' => false,
            'height' => 'auto',
            'eventResize' => false, // Không cho phép resize
        ];
    }

    protected int | string | array $columnSpan = 'full';

    public function onEventClick($event): void
    {
        $taskId = $event['event']['extendedProps']['task_id'] ?? null;
        if ($taskId) {
            $this->redirect(route('filament.admin.resources.tasks.edit', $taskId));
        }
    }

    /**
     * Xử lý khi kéo thả task để đổi deadline
     * 
     * @param array $event Event object sau khi drop
     * @param array $oldEvent Event object trước khi drop
     * @param array $relatedEvents Các event liên quan
     * @param array $delta Thời gian đã di chuyển
     * @param ?array $oldResource Resource cũ (nếu có)
     * @param ?array $newResource Resource mới (nếu có)
     * @return bool true để revert (hoàn tác), false để giữ nguyên
     */
    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $taskId = $event['extendedProps']['task_id'] ?? null;
        $newDate = $event['start'] ?? null;
        
        if ($taskId && $newDate) {
            $task = Task::find($taskId);
            if ($task) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                
                // Kiểm tra quyền update
                if ($user && $user->can('update', $task)) {
                    try {
                        // Parse date từ FullCalendar format
                        $dueDate = is_string($newDate) ? Carbon::parse($newDate) : $newDate;
                        
                        $task->update([
                            'due_date' => $dueDate->format('Y-m-d'),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Thành công')
                            ->body('Đã cập nhật deadline thành công!')
                            ->success()
                            ->send();
                        
                        // Return false để giữ nguyên vị trí mới
                        return false;
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Lỗi')
                            ->body('Không thể cập nhật deadline: ' . $e->getMessage())
                            ->danger()
                            ->send();
                        
                        // Return true để revert về vị trí cũ
                        return true;
                    }
                } else {
                    \Filament\Notifications\Notification::make()
                        ->title('Lỗi')
                        ->body('Bạn không có quyền cập nhật công việc này!')
                        ->danger()
                        ->send();
                    
                    // Return true để revert về vị trí cũ
                    return true;
                }
            }
        }
        
        // Nếu không có taskId hoặc newDate, revert
        return true;
    }
}

