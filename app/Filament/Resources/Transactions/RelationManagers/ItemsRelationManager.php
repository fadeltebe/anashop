<?php

namespace App\Filament\Resources\Transactions\RelationManagers;


use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\Transactions\Resources\TransactionItems\TransactionItemResource;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $relatedResource = TransactionItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
