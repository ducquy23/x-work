<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Biểu đồ công việc theo trạng thái';

    protected static ?int $sort = 2;

    public function getHeading(): ?string
    {
        return static::$heading;
    }

    protected function getData(): array
    {
        $tasks = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng công việc',
                    'data' => [
                        $tasks['new'] ?? 0,
                        $tasks['in_progress'] ?? 0,
                        $tasks['review'] ?? 0,
                        $tasks['completed'] ?? 0,
                        $tasks['cancelled'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // new - gray
                        'rgb(59, 130, 246)', // in_progress - blue
                        'rgb(251, 191, 36)', // review - yellow
                        'rgb(34, 197, 94)', // completed - green
                        'rgb(239, 68, 68)', // cancelled - red
                    ],
                ],
            ],
            'labels' => ['Mới', 'Đang thực hiện', 'Đang xem xét', 'Hoàn thành', 'Đã hủy'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

