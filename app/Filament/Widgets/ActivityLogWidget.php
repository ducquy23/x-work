<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Nhật ký hoạt động của tôi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->where('causer_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
            )
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label('Hoạt động')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Loại')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->heading('Nhật ký hoạt động của tôi')
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

