<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = auth()->id();
        if (isset($data['assignees'])) {
            $assignees = $data['assignees'];
            unset($data['assignees']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->data['assignees'])) {
            $this->record->assignees()->sync($this->data['assignees']);
        }
    }
}
