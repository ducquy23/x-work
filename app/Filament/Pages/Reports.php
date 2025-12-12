<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Báo cáo & KPI';

    protected static ?string $navigationGroup = 'Báo cáo';

    protected static ?int $navigationSort = 7;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\KPIDashboardWidget::class,
            \App\Filament\Widgets\DepartmentPerformanceWidget::class,
            \App\Filament\Widgets\TasksByPriorityWidget::class,
        ];
    }
}

