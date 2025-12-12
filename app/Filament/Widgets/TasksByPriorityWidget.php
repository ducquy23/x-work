<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksByPriorityWidget extends ChartWidget
{
    protected static ?string $heading = 'Phân tích công việc theo độ ưu tiên';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return static::$heading;
    }

    protected function getData(): array
    {
        $tasks = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng công việc',
                    'data' => [
                        $tasks['urgent'] ?? 0,
                        $tasks['high'] ?? 0,
                        $tasks['medium'] ?? 0,
                        $tasks['low'] ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgb(239, 68, 68)', // urgent - red
                        'rgb(245, 158, 11)', // high - orange
                        'rgb(59, 130, 246)', // medium - blue
                        'rgb(107, 114, 128)', // low - gray
                    ],
                ],
            ],
            'labels' => ['Khẩn', 'Cao', 'Trung bình', 'Thấp'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

