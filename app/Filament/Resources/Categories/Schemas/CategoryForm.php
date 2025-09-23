<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('total_products')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('description'),
                FileUpload::make('icon')
                    ->directory('categories')
                    ->image()
                    ->enableOpen()
                    ->enableDownload()
                    ->imageEditor()
                    ->disk('public')
                // ->deleteUploadedFileUsing(function ($file) {
                //     Storage::disk('public')->delete($file);
                // })
                ,

            ]);
    }
}
