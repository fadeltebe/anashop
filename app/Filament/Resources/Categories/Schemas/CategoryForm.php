<?php

namespace App\Filament\Resources\Categories\Schemas;

use Laravel\Pail\File;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),


                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                TextInput::make('total_products')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('description'),
                FileUpload::make('icon')
                    ->image()
                    ->label('Ikon Kategori (Emoji atau Gambar)')
                    ->directory('category-icons')
                    ->disk('public')
                    ->visibility('public'),
            ]);
    }
}
