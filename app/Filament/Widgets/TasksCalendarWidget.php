<?php

namespace App\Filament\Widgets;

use App\Models\Task;
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
        return Task::query()
            ->whereNotNull('due_date')
            ->get()
            ->map(function (Task $task) {
                $color = match ($task->priority) {
                    'urgent' => '#ef4444',
                    'high' => '#f59e0b',
                    'medium' => '#3b82f6',
                    'low' => '#6b7280',
                    default => '#6b7280',
                };

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d'),
                    'end' => $task->due_date->format('Y-m-d'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'task_id' => $task->id,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'assignee' => $task->assignee?->name,
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
            'editable' => true,
            'selectable' => true,
            'droppable' => false,
            'height' => 'auto',
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
}

