<?php

namespace App\Filament\Resources\TransactionItems;

use App\Filament\Resources\TransactionItems\Pages\CreateTransactionItem;
use App\Filament\Resources\TransactionItems\Pages\EditTransactionItem;
use App\Filament\Resources\TransactionItems\Pages\ListTransactionItems;
use App\Filament\Resources\TransactionItems\Schemas\TransactionItemForm;
use App\Filament\Resources\TransactionItems\Tables\TransactionItemsTable;
use App\Models\TransactionItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionItemResource extends Resource
{
    protected static ?string $model = TransactionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionItems::route('/'),
            'create' => CreateTransactionItem::route('/create'),
            'edit' => EditTransactionItem::route('/{record}/edit'),
        ];
    }
}
