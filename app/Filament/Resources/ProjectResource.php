<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Dự án';

    protected static ?string $modelLabel = 'Dự án';

    protected static ?string $pluralModelLabel = 'Dự án';

    protected static ?string $navigationGroup = 'Quản lý';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin dự án')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên dự án')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('department_id')
                            ->label('Phòng ban')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Thời gian dự án')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Ngày kết thúc')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->after('start_date'),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'planning' => 'Lập kế hoạch',
                                'in_progress' => 'Đang thực hiện',
                                'on_hold' => 'Tạm dừng',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                            ])
                            ->default('planning')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên dự án')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Phòng ban')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Ngày kết thúc')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Số công việc')
                    ->counts('tasks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planning' => 'gray',
                        'in_progress' => 'info',
                        'on_hold' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planning' => 'Lập kế hoạch',
                        'in_progress' => 'Đang thực hiện',
                        'on_hold' => 'Tạm dừng',
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
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Phòng ban')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'planning' => 'Lập kế hoạch',
                        'in_progress' => 'Đang thực hiện',
                        'on_hold' => 'Tạm dừng',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
