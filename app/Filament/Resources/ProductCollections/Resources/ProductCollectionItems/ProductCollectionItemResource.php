<?php

namespace App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems;

use App\Filament\Resources\ProductCollections\ProductCollectionResource;
use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Pages\CreateProductCollectionItem;
use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Pages\EditProductCollectionItem;
use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Schemas\ProductCollectionItemForm;
use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Tables\ProductCollectionItemsTable;
use App\Models\ProductCollectionItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductCollectionItemResource extends Resource
{
    protected static ?string $model = ProductCollectionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = ProductCollectionResource::class;

    public static function form(Schema $schema): Schema
    {
        return ProductCollectionItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCollectionItemsTable::configure($table);
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
            'create' => CreateProductCollectionItem::route('/create'),
            'edit' => EditProductCollectionItem::route('/{record}/edit'),
        ];
    }
}
