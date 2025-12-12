<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class KPIDashboardWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'KPI Dashboard';
    }

    protected function getStats(): array
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $onTimeTasks = Task::where('status', 'completed')
            ->whereColumn('completed_at', '<=', 'due_date')
            ->count();
        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
        $onTimeRate = $completedTasks > 0 ? round(($onTimeTasks / $completedTasks) * 100, 1) : 0;

        // Tính KPI trung bình cho từng user
        $userKPIs = User::withCount([
            'tasksAssigned as completed_tasks' => function ($query) {
                $query->where('status', 'completed');
            },
            'tasksAssigned as overdue_tasks' => function ($query) {
                $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
            },
        ])->get();

        $avgCompletionRate = $userKPIs->avg(function ($user) {
            $total = $user->tasksAssigned()->count();
            return $total > 0 ? ($user->completed_tasks / $total) * 100 : 0;
        });

        return [
            Stat::make('Tỷ lệ hoàn thành', $completionRate . '%')
                ->description('Công việc đã hoàn thành / Tổng công việc')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([70, 75, 80, 85, 90, $completionRate]),
            Stat::make('Tỷ lệ đúng hạn', $onTimeRate . '%')
                ->description('Hoàn thành đúng hạn / Tổng hoàn thành')
                ->descriptionIcon('heroicon-m-clock')
                ->color($onTimeRate >= 80 ? 'success' : 'warning')
                ->chart([75, 78, 82, 85, 88, $onTimeRate]),
            Stat::make('Công việc trễ hạn', $overdueTasks)
                ->description('Cần xử lý ngay')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([5, 4, 3, 2, 1, $overdueTasks]),
            Stat::make('KPI trung bình', round($avgCompletionRate, 1) . '%')
                ->description('Điểm KPI trung bình của nhân viên')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary')
                ->chart([65, 70, 75, 80, 85, round($avgCompletionRate, 1)]),
        ];
    }
}

