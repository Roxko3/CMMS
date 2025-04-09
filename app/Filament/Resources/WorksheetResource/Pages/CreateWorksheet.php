<?php

namespace App\Filament\Resources\WorksheetResource\Pages;

use App\Filament\Resources\WorksheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorksheet extends CreateRecord
{
    protected static string $resource = WorksheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['creator_id']))
            $data['creator_id'] = auth()->user()->id;
        return $data;
    }
}
