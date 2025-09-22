<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('store_name')
                    ->required()
                    ->default('Ana Shop'),
                TextInput::make('address'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->directory('settings'),
                TextInput::make('favicon'),
                TextInput::make('facebook'),
                TextInput::make('instagram'),
                TextInput::make('whatsapp'),
            ]);
    }
}
