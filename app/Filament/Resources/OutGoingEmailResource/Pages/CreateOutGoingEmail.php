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
        $template = EmailTemplate::where('key', $data['email_template_key'])->firstOrFail();

        // Replace tokens in the content
        $html = $template->content;
        foreach ($data['email_tokens'] ?? [] as $key => $value) {
            $html = str_replace("{{ $key }}", $value, $html);
        }

        // Handle multiple recipients (comma-separated)
        $emails = array_filter(array_map('trim', explode(',', $data['recipient_emails'])));

        try {
            Mail::html($html, function ($message) use ($emails, $template) {
                $message->to($emails)
                        ->subject($template->subject);
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title('Email sending failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }

        // Save final values to DB
        $data['html_body'] = $html;
        $data['subject'] = $template->subject;
        $data['to'] = implode(', ', $emails);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
