<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.pages.profile';

    protected static ?string $navigationLabel = 'Không gian cá nhân';

    protected static ?string $navigationGroup = 'Cá nhân';

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cá nhân')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Họ và tên')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->label('Phòng ban')
                            ->relationship('department', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('position')
                            ->label('Chức vụ')
                            ->disabled(),
                        CuratorPicker::make('avatar')
                            ->label('Ảnh đại diện')
                            ->directory('avatars')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->buttonLabel('Chọn ảnh đại diện')
                            ->listDisplay(false),
                    ])->columns(2),
            ])
            ->statePath('data')
            ->model(Auth::user());
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\MyTasksWidget::class,
            \App\Filament\Widgets\ActivityLogWidget::class,
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Auth::user()->update($data);
        $this->form->fill(Auth::user()->toArray());
        
        Notification::make()
            ->title('Thành công')
            ->body('Đã cập nhật thông tin thành công!')
            ->success()
            ->send();
    }
}

