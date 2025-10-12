<?php

namespace App\Filament\Resources\Transactions\Resources\TransactionItems;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\TransactionItem;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\Transactions\Resources\TransactionItems\Pages\EditTransactionItem;
use App\Filament\Resources\Transactions\Resources\TransactionItems\Pages\CreateTransactionItem;
use App\Filament\Resources\Transactions\Resources\TransactionItems\Schemas\TransactionItemForm;
use App\Filament\Resources\Transactions\Resources\TransactionItems\Tables\TransactionItemsTable;

class TransactionItemResource extends Resource
{
    protected static ?string $model = TransactionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = TransactionResource::class;

    public static function form(Schema $schema): Schema
    {
        return TransactionItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateTransactionItem::route('/create'),
            'edit' => EditTransactionItem::route('/{record}/edit'),
        ];
    }
}
