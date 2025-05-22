<?php

namespace App\Filament\Resources\OutGoingEmailResource\Pages;

use App\Filament\Resources\OutGoingEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOutGoingEmails extends ListRecords
{
    protected static string $resource = OutGoingEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
