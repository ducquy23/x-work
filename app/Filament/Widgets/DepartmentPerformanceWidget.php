<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class DepartmentPerformanceWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Hiệu suất theo phòng ban';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Department::query()
                    ->withCount([
                        'users',
                        'projects',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Phòng ban')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Số nhân viên')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Số dự án')
                    ->counts('projects')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tasks')
                    ->label('Tổng công việc')
                    ->getStateUsing(function (Department $record) {
                        return Task::whereHas('project', function ($query) use ($record) {
                            $query->where('department_id', $record->id);
                        })->count();
                    }),
                Tables\Columns\TextColumn::make('completed_tasks')
                    ->label('Đã hoàn thành')
                    ->getStateUsing(function (Department $record) {
                        return Task::whereHas('project', function ($query) use ($record) {
                            $query->where('department_id', $record->id);
                        })->where('status', 'completed')->count();
                    }),
                Tables\Columns\TextColumn::make('completion_rate')
                    ->label('Tỷ lệ hoàn thành')
                    ->getStateUsing(function (Department $record) {
                        $totalTasks = Task::whereHas('project', function ($query) use ($record) {
                            $query->where('department_id', $record->id);
                        })->count();
                        
                        if ($totalTasks == 0) return '0%';
                        
                        $completed = Task::whereHas('project', function ($query) use ($record) {
                            $query->where('department_id', $record->id);
                        })->where('status', 'completed')->count();
                        
                        return round(($completed / $totalTasks) * 100, 1) . '%';
                    })
                    ->color(function ($state) {
                        $rate = (float) str_replace('%', '', $state);
                        return $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                    }),
            ])
            ->heading('Hiệu suất theo phòng ban')
            ->defaultSort('name', 'asc');
    }
}

