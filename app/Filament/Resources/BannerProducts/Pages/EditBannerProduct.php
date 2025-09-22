<?php

namespace App\Filament\Resources\BannerProducts\Pages;

use App\Filament\Resources\BannerProducts\BannerProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBannerProduct extends EditRecord
{
    protected static string $resource = BannerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
