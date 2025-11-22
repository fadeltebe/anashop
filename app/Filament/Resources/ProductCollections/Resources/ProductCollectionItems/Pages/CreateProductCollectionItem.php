<?php

namespace App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\Pages;

use App\Filament\Resources\ProductCollections\Resources\ProductCollectionItems\ProductCollectionItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductCollectionItem extends CreateRecord
{
    protected static string $resource = ProductCollectionItemResource::class;
}
