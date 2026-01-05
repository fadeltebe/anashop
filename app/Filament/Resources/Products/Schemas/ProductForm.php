<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use SebastianBergmann\CodeUnit\FileUnit;

use function Laravel\Prompts\select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([

                Select::make('owner')
                    ->options([
                        'Uma Alawi' => 'Uma Alawi',
                        'Mama Zahra' => 'Mama Zahra',
                    ])
                    ->required()
                    ->default('Mama Zahra'),
                Select::make('category_id')
                    ->label('Kategori Produk')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        // Generate ulang code saat kategori berubah
                        $name = $get('name');
                        if ($name) {
                            $code = self::generateProductCodeWithCategory($state, $name);
                            $set('code', $code);
                        }
                    }),



                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, $get, $set) {
                        if ($operation === 'create') {
                            // Generate slug
                            $set('slug', \Illuminate\Support\Str::slug($state));

                            // Generate code dengan kategori
                            $categoryId = $get('category_id');
                            if ($categoryId) {
                                $code = self::generateProductCodeWithCategory($categoryId, $state);
                                $set('code', $code);
                            }
                        }
                    })
                    ->maxLength(255),

                TextInput::make('code')
                    ->required()
                    ->label('Kode Produk')
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Format: [Kategori]-[Produk][0001]')
                    ->maxLength(50),


                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->hidden()
                    ->helperText('Slug akan otomatis dibuat dari nama produk.')
                    ->readonly(),
                TextInput::make('price')
                    ->label('Harga Normal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp '),
                TextInput::make('discount_price')
                    ->label('Harga Diskon')
                    ->numeric()
                    ->prefix('Rp '),
                TextInput::make('stock')
                    ->required()
                    ->default(10)
                    ->numeric(),
                TextInput::make('total_sales')
                    ->label('Total Terjual')
                    // ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('weight')
                    ->label('Berat Produk')
                    // ->required()
                    ->numeric()
                    ->default(200)
                    ->suffix('gram'),
                TextInput::make('rating')
                    ->default(5)
                    // ->required()
                    ->numeric(),
                TextInput::make('rating_count')
                    // ->required()
                    ->numeric()
                    ->default(5),
                Textarea::make('description')
                    ->label('Deskripsi Produk')
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->label('Foto Utama')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048) // Maksimum 1MB
                    ->directory('product-thumbnails')
                    ->disk('public')
                    ->visibility('public')
                    ->columnSpanFull(),
                FileUpload::make('photos')
                    ->label('Foto Tambahan')
                    ->image()
                    ->maxSize(2048) // Maksimum 2MB per foto
                    ->directory('product-photos')
                    ->disk('public')
                    ->multiple()
                    ->reorderable()
                    ->panelLayout('grid') // tampil dalam grid
                    ->visibility('public')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Tampilkan Produk?')
                    ->default(true)
                    ->required(),

            ]);
    }

    protected static function generateProductCodeWithCategory(int $categoryId, string $productName): string
    {
        // Ambil kode kategori
        $category = \App\Models\Category::find($categoryId);
        $categoryCode = strtoupper(substr($category->name, 0, 3));

        // Ambil inisial produk (3 huruf pertama dari setiap kata)
        $words = explode(' ', $productName);
        $productCode = '';

        foreach (array_slice($words, 0, 3) as $word) {
            $productCode .= strtoupper(substr($word, 0, 1));
        }

        if (strlen($productCode) < 3) {
            $productCode = strtoupper(substr($productName, 0, 3));
        }

        $prefix = $categoryCode . '-' . $productCode;

        // Cari nomor urut terakhir
        $lastProduct = \App\Models\Product::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastProduct) {
            preg_match('/(\d+)$/', $lastProduct->code, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
