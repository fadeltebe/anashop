<?php

namespace App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Models\Product;

class ProductCollectionItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Pilih Produk
            Select::make('product_id')
                ->label('Produk')
                ->searchable()
                ->required()
                ->getSearchResultsUsing(function (string $search): array {
                    return Product::where('name', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->getOptionLabelUsing(fn($value): ?string => Product::find($value)?->name)
                ->preload(),

            // Urutan item dalam koleksi
            TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0)
                ->required(),

            // Status
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }
}
