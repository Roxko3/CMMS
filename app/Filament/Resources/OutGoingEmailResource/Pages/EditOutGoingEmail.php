<?php

namespace App\Filament\Resources\OutGoingEmailResource\Pages;

use App\Filament\Resources\OutGoingEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOutGoingEmail extends EditRecord
{
    protected static string $resource = OutGoingEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
