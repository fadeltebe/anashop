<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->helperText('Slug akan otomatis dibuat dari nama kategori.')
                    ->readOnly()
                    ->hidden(),

                TextInput::make('total_products')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Otomatis dihitung dari jumlah produk.'),

                TextInput::make('description')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Aktifkan Kategori'),


                FileUpload::make('icon')
                    ->directory('categories')
                    ->image()
                    ->enableOpen()
                    ->enableDownload()
                    ->imageEditor()
                    ->disk('public')
                    ->maxSize(2048)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1'),
            ]);
    }
}
