<?php

namespace App\Filament\Resources\Products\Resources\ProductItems;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Resources\ProductItems\Pages\CreateProductItem;
use App\Filament\Resources\Products\Resources\ProductItems\Pages\EditProductItem;
use App\Filament\Resources\Products\Resources\ProductItems\Schemas\ProductItemForm;
use App\Filament\Resources\Products\Resources\ProductItems\Tables\ProductItemsTable;
use App\Models\ProductItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductItemResource extends Resource
{
    protected static ?string $model = ProductItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = ProductResource::class;

    public static function form(Schema $schema): Schema
    {
        return ProductItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductItemsTable::configure($table);
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
            'create' => CreateProductItem::route('/create'),
            'edit' => EditProductItem::route('/{record}/edit'),
        ];
    }
}
