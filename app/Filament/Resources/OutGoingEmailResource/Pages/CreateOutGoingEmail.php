<?php

namespace App\Filament\Resources\OutGoingEmailResource\Pages;

use App\Filament\Resources\OutGoingEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateOutGoingEmail extends CreateRecord
{
    protected static string $resource = OutGoingEmailResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Send the email before saving
        Mail::html($data['html_body'], function ($message) use ($data) {
            $message->to($data['to'])
                ->subject($data['subject']);
        });

        return $data;
    }
}
