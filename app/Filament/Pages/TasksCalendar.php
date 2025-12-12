<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TasksCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.tasks-calendar';

    protected static ?string $navigationLabel = 'Lịch làm việc';

    protected static ?string $navigationGroup = 'Công việc';

    protected static ?int $navigationSort = 6;

    protected function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\TasksCalendarWidget::class,
        ];
    }
}

