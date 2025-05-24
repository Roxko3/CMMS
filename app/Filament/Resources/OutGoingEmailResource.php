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

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';


    public static function getNavigationGroup(): string
    {
        return __('module_names.navigation_groups.email');
    }

    public static function getModelLabel(): string
    {
        return __('module_names.email.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('module_names.email.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Recipient configuration
                Forms\Components\Textarea::make('recipient_emails')
                ->label(__('fields.email_recepient'))
                ->required()
                ->helperText(__('fields.recepient_helpertext'))
                ->rows(2),

                // Email Template Selection
                Forms\Components\Select::make('email_template_key')
                    ->label(__('fields.email_template'))
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
                    ->label(__('fields.email_subject'))
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn($get) => filled($get('email_template_key'))),

                // Preview the template content (first 200 chars)
                Forms\Components\Textarea::make('email_content_preview')
                    ->label(__('fields.email_content'))
                    ->disabled()
                    ->dehydrated(false)
                    ->rows(4)
                    ->visible(fn($get) => filled($get('email_template_key'))),

                // Additional fields for customization
                Forms\Components\KeyValue::make('email_tokens')
                    ->label(__('fields.email_tokens'))
                    ->helperText(__('fields.token_helpertext'))
                    ->keyLabel(__('fields.token_name'))
                    ->valueLabel(__('fields.token_value'))
                    ->addActionLabel(__('fields.add_token'))
                    ->default([])
                    ->visible(fn($get) => filled($get('email_template_key'))),


            ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('to')->label(__('fields.email_recepient')),
                Tables\Columns\TextColumn::make('subject')->label(__('fields.email_subject')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('fields.created_at')),
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
