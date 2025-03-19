<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('module_names.navigation_groups.administration');
    }

    public static function getModelLabel(): string
    {
        return __('module_names.devices.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('module_names.devices.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')->label(__('fields.name'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
                Forms\Components\TextInput::make('erp_code')->label(__('fields.erp_code'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
                Forms\Components\Select::make('type_id')->label(__('fields.type'))
                ->relationship('type', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                Forms\Components\TextInput::make('name')->label(__('fields.name'))
                ->required()
                ->unique()
                ->maxLength(255)
                ])
                ->required(),
                Forms\Components\TextInput::make('plant')->label(__('fields.plant'))
                ->required()
                ->maxLength(255),
                Forms\Components\Toggle::make('active')->label(__('fields.active'))
                ->onColor('success')
                ->offColor('danger')
                ->columnSpan('full'),
                Forms\Components\TextInput::make('history')->label(__('fields.history'))
                ->maxLength(255),
                Forms\Components\TextInput::make('note')->label(__('fields.note'))
                ->maxLength(255),
                ])
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('name')->label(__('fields.name')),
            TextEntry::make('erp_code')->label(__('fields.erp_code')),
            TextEntry::make('type.name')->label(__('fields.type')),
            TextEntry::make('plant')->label(__('fields.plant')),
            TextEntry::make('active')->label(__('fields.active'))
            ->state(function (Model $record): string {
            return $record->active ? __('fields.yes') : __('fields.no');
            }),
            TextEntry::make('history')->label(__('fields.history')),
            TextEntry::make('notes')->label(__('fields.note')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('fields.name'))
                ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('erp_code')->label(__('fields.erp_code'))
                ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type.name')->label(__('fields.type'))
                ->searchable()->sortable(),
                Tables\Columns\IconColumn::make('active')->label(__('fields.active'))
                ->boolean()
                ->action(function($record, $column) {
                $name = $column->getName();
                $record->update([
                $name => !$record->$name
                ]);
                }),
                Tables\Columns\TextColumn::make('created_at')->label(__('fields.created_at'))
                ->dateTime('Y-m-d H:i')
                ->searchable()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('QR')->label(__('fields.qr_code'))
                ->modalContent(fn ($record): View => view('filament.resources.device-resource.pages.q-r-device', ['record' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'view' => Pages\ViewDevice::route('/{record}'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
            'qr' => Pages\QRDevice::route('/qr/{record}'),
        ];
    }
}
