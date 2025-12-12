<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Công việc';

    protected static ?string $modelLabel = 'Công việc';

    protected static ?string $pluralModelLabel = 'Công việc';

    protected static ?string $navigationGroup = 'Công việc';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin công việc')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('project_id')
                            ->label('Dự án')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('assignee_id')
                            ->label('Người được giao')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->label('Độ ưu tiên')
                            ->options([
                                'urgent' => 'Khẩn',
                                'high' => 'Cao',
                                'medium' => 'Trung bình',
                                'low' => 'Thấp',
                            ])
                            ->default('medium')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Hạn hoàn thành')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'new' => 'Mới',
                                'in_progress' => 'Đang thực hiện',
                                'review' => 'Đang xem xét',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                            ])
                            ->default('new')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Tiến độ')
                    ->schema([
                        Forms\Components\TextInput::make('progress')
                            ->label('Tiến độ (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->required(),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Ngày hoàn thành')
                            ->displayFormat('d/m/Y H:i')
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Dự án')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Người được giao')
                    ->sortable()
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
                    ->sortable()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Hạn hoàn thành')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'completed' ? 'danger' : null),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Dự án')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('assignee_id')
                    ->label('Người được giao')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Độ ưu tiên')
                    ->options([
                        'urgent' => 'Khẩn',
                        'high' => 'Cao',
                        'medium' => 'Trung bình',
                        'low' => 'Thấp',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'new' => 'Mới',
                        'in_progress' => 'Đang thực hiện',
                        'review' => 'Đang xem xét',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Trễ hạn')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())
                        ->where('status', '!=', 'completed')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChecklistsRelationManager::class,
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\FilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
