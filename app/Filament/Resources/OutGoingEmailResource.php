<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OutGoingEmailResource\Pages;
use App\Filament\Resources\OutGoingEmailResource\RelationManagers;
use App\Models\OutGoingEmail;
use App\Models\OutGoingMail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Visualbuilder\EmailTemplates\Models\EmailTemplate;

class OutGoingEmailResource extends Resource
{
    protected static ?string $model = OutGoingMail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic form fields
                Forms\Components\TextInput::make('name')
                    ->required(),

                // Email Template Selection
                Forms\Components\Select::make('email_template_key')
                    ->label('Email Template')
                    ->options(function () {
                        return EmailTemplate::all()
                            ->pluck('title', 'key')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $template = EmailTemplate::where('key', $state)->first();
                            if ($template) {
                                $set('email_subject', $template->subject);
                                $set('email_content_preview', strip_tags($template->content));
                            }
                        }
                    }),

                // Preview the selected template subject
                Forms\Components\TextInput::make('email_subject')
                    ->label('Email Subject Preview')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn($get) => filled($get('email_template_key'))),

                // Preview the template content (first 200 chars)
                Forms\Components\Textarea::make('email_content_preview')
                    ->label('Email Content Preview')
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(4)
                    ->visible(fn($get) => filled($get('email_template_key'))),

                // Additional fields for customization
                Forms\Components\KeyValue::make('email_tokens')
                    ->label('Email Template Tokens')
                    ->helperText('Define custom tokens for this email (e.g., customer_name, order_number)')
                    ->keyLabel('Token Name')
                    ->valueLabel('Token Value')
                    ->addActionLabel('Add Token')
                    ->default([])
                    ->visible(fn($get) => filled($get('email_template_key'))),

                // Recipient configuration
                Forms\Components\TextInput::make('recipient_email')
                    ->label('Recipient Email')
                    ->email()
                    ->required(),

                // Language selection (if multilingual)
                Forms\Components\Select::make('language')
                    ->label('Language')
                    ->options([
                        'en_GB' => 'British English',
                        'en_US' => 'American English',
                        'es' => 'Español',
                        'fr' => 'Français',
                    ])
                    ->default('en_GB')
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        // Reload template content for selected language
                        $templateKey = $get('email_template_key');
                        if ($templateKey && $state) {
                            $template = EmailTemplate::where('key', $templateKey)
                                ->where('language', $state)
                                ->first();
                            if ($template) {
                                $set('email_subject', $template->subject);
                                $set('email_content_preview', strip_tags($template->content));
                            }
                        }
                    }),

                // Send immediately option
                Forms\Components\Toggle::make('send_immediately')
                    ->label('Send Email Immediately')
                    ->default(false),

                // Schedule sending
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Schedule Send Time')
                    ->visible(fn($get) => !$get('send_immediately'))
                    ->native(false),
            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('to'),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListOutGoingEmails::route('/'),
            'create' => Pages\CreateOutGoingEmail::route('/create'),
            'edit' => Pages\EditOutGoingEmail::route('/{record}/edit'),
        ];
    }
}
