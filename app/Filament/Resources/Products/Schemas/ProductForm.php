<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use SebastianBergmann\CodeUnit\FileUnit;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp '),
                TextInput::make('discount_price')
                    ->numeric(),
                TextInput::make('stock')
                    ->required()
                    ->numeric(),
                TextInput::make('total_sales')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('rating_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->image()
                    ->maxSize(1024) // Maksimum 1MB
                    ->directory('product-thumbnails')
                    ->disk('public')
                    ->visibility('public')
                    ->columnSpanFull(),
                FileUpload::make('photos')
                    ->image()
                    ->maxSize(2048) // Maksimum 2MB per foto
                    ->directory('product-photos')
                    ->disk('public')
                    ->multiple()
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->required(),
                Toggle::make('is_live')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_flash_sale')
                    ->required(),
            ]);
    }
}
