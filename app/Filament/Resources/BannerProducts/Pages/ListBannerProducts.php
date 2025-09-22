<?php

namespace App\Filament\Resources\BannerProducts\Pages;

use App\Filament\Resources\BannerProducts\BannerProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBannerProducts extends ListRecords
{
    protected static string $resource = BannerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
