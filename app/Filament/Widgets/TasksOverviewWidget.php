<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TasksOverviewWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return 'Tổng quan công việc';
    }

    protected function getStats(): array
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        return [
            Stat::make('Tổng công việc', $totalTasks)
                ->description('Tất cả công việc')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            Stat::make('Đang thực hiện', $inProgressTasks)
                ->description('Công việc đang làm')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->chart([3, 2, 4, 3, 5, 4, 6]),
            Stat::make('Hoàn thành', $completedTasks)
                ->description('Đã hoàn thành')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 3, 4, 5, 6, 7, 8]),
            Stat::make('Trễ hạn', $overdueTasks)
                ->description('Cần xử lý ngay')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([1, 2, 1, 3, 2, 4, 3]),
        ];
    }
}

