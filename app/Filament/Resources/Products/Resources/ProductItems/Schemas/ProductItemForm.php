<?php

namespace App\Filament\Resources\Products\Resources\ProductItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ProductItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
                Select::make('owner')
                    ->required()
                    ->options([
                        'ana' => 'Ana',
                        'uma_alawi' => 'Uma Alawi',
                    ])
                    ->default('admin'),
                TextInput::make('variant'),
                TextInput::make('size'),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('total_sales')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->image(),
            ]);
    }
}
