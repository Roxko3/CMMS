<?php

namespace App\Filament\Resources\OutGoingEmailResource\Pages;

use App\Filament\Resources\OutGoingEmailResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Visualbuilder\EmailTemplates\Models\EmailTemplate;
use Filament\Notifications\Notification;

class CreateOutGoingEmail extends CreateRecord
{
    protected static string $resource = OutGoingEmailResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Fetch the email template
        $template = EmailTemplate::where('key', $data['email_template_key'])->firstOrFail();

        // Replace tokens in the template content
        $html = $template->content;
        foreach ($data['email_tokens'] ?? [] as $key => $value) {
            $html = str_replace("{{ $key }}", $value, $html);
        }

        // Send the email
        try {
            Mail::html($html, function ($message) use ($data, $template) {
                $message->to($data['recipient_email'])
                        ->subject($template->subject);
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('Email sending failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Optionally prevent saving if mail fails
            throw $e;
        }

        // Set fields to be saved in the database
        $data['html_body'] = $html;
        $data['subject'] = $template->subject;
        $data['to'] = $data['recipient_email'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
