<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected ?array $assigneesToSync = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['assignees'])) {
            $this->assigneesToSync = $data['assignees'];
            unset($data['assignees']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->assigneesToSync)) {
            $this->record->assignees()->sync($this->assigneesToSync);
            unset($this->assigneesToSync);
        }
    }
}
