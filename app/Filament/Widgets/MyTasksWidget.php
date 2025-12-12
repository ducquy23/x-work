<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyTasksWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Công việc của tôi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('assignee_id', auth()->id())
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Dự án')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('progress')
                    ->label('Tiến độ')
                    ->numeric()
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Hạn hoàn thành')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'completed' ? 'danger' : null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'in_progress' => 'info',
                        'review' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Mới',
                        'in_progress' => 'Đang thực hiện',
                        'review' => 'Đang xem xét',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record])),
            ])
            ->heading('Công việc của tôi');
    }
}

