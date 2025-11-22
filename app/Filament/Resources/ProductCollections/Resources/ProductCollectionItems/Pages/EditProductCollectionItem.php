<?php

namespace App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Pages;

use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\ProductCollectionItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductCollectionItem extends EditRecord
{
    protected static string $resource = ProductCollectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
