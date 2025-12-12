<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueTasksWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Công việc trễ hạn';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Người được giao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Hạn hoàn thành')
                    ->date('d/m/Y')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Ưu tiên')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'urgent' => 'Khẩn',
                        'high' => 'Cao',
                        'medium' => 'TB',
                        'low' => 'Thấp',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Trễ (ngày)')
                    ->getStateUsing(function (Task $record) {
                        return now()->diffInDays($record->due_date);
                    })
                    ->color('danger')
                    ->suffix(' ngày'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record])),
            ])
            ->heading('Công việc trễ hạn');
    }
}

