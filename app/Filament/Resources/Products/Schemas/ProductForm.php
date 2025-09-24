<?php

namespace App\Filament\Resources\Products\Schemas;

use Faker\Core\File;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp.'),
                TextInput::make('discount_price')
                    ->required()
                    ->numeric(),
                TextInput::make('stock')
                    ->required()
                    ->numeric(),
                TextInput::make('total_sales')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('thumbnail')
                    ->directory('products')
                    ->visibility('public')
                    ->disk('public')
                    ->required(),
                FileUpload::make('photos')
                    ->disk('public')
                    ->multiple()
                    ->directory('products')
                    ->visibility('public'),
                TextInput::make('description')
                    ->required()
                    ->maxLength(65535),
            ]);
    }
}
