<?php

namespace App\Filament\Resources\ProductCollections;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\ProductCollection;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\ProductCollections\Pages\EditProductCollection;
use App\Filament\Resources\ProductCollections\Pages\ListProductCollections;
use App\Filament\Resources\ProductCollections\Pages\CreateProductCollection;
use App\Filament\Resources\ProductCollections\Schemas\ProductCollectionForm;
use App\Filament\Resources\ProductCollections\Tables\ProductCollectionsTable;
use App\Filament\Resources\ProductCollections\RelationManagers\ItemsRelationManager;

class ProductCollectionResource extends Resource
{
    protected static ?string $model = ProductCollection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductCollectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCollectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'items' => ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductCollections::route('/'),
            'create' => CreateProductCollection::route('/create'),
            'edit' => EditProductCollection::route('/{record}/edit'),
        ];
    }
}
