<?php

namespace App\Filament\Resources\ProductCollections\RelationManagers;

use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\ProductCollectionItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $relatedResource = ProductCollectionItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
