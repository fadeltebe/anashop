<?php

namespace App\Filament\Resources\BannerProducts;

use App\Filament\Resources\BannerProducts\Pages\CreateBannerProduct;
use App\Filament\Resources\BannerProducts\Pages\EditBannerProduct;
use App\Filament\Resources\BannerProducts\Pages\ListBannerProducts;
use App\Filament\Resources\BannerProducts\Schemas\BannerProductForm;
use App\Filament\Resources\BannerProducts\Tables\BannerProductsTable;
use App\Models\BannerProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BannerProductResource extends Resource
{
    protected static ?string $model = BannerProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BannerProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BannerProductsTable::configure($table);
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
            'index' => ListBannerProducts::route('/'),
            'create' => CreateBannerProduct::route('/create'),
            'edit' => EditBannerProduct::route('/{record}/edit'),
        ];
    }
}
