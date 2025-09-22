<?php

namespace App\Filament\Resources\BannerProducts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BannerProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('banner_id')
                    ->required()
                    ->numeric(),
                TextInput::make('product_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
