<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\AttributeValue;
use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Wizard::make([

                /* ======================================================
                 * STEP 1 — DATA PRODUK
                 * ====================================================== */
                Step::make('Produk')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('owner')
                                ->options([
                                    'Uma Alawi' => 'Uma Alawi',
                                    'Mama Zahra' => 'Mama Zahra',
                                ])
                                ->default('Mama Zahra')
                                ->required(),

                            // =========================
                            // CATEGORY
                            // =========================
                            Select::make('category_id')
                                ->label('Kategori Produk')
                                ->relationship('category', 'name')
                                // ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Update slug
                                $set('slug', Str::slug($state));

                                // ✅ Update SKU semua varian jika ada
                                if ($get('has_variant') && !empty($get('variants'))) {
                                    $variants = $get('variants');
                                    $updatedVariants = collect($variants)->map(function ($variant) use ($state) {
                                        $variant['sku'] = strtoupper(Str::slug($state . '-' . $variant['variant_name']));
                                        return $variant;
                                    })->toArray();

                                    $set('variants', $updatedVariants);
                                }

                                // ✅ Update SKU single product jika ada
                                if (!$get('has_variant') && $state) {
                                    $set('single.sku', strtoupper(Str::slug($state)));
                                }
                            }),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->hidden(),

                        TextInput::make('rating')
                            ->label('Rating Produk')
                            ->numeric()
                            ->dehydrated()
                            ->default(5),
                        TextInput::make('rating_count')
                            ->label('Jumlah Rating')
                            ->dehydrated()
                            ->default(0),

                        Grid::make(2)->schema([
                            Textarea::make('description')
                                ->label('Deskripsi Produk'),

                            FileUpload::make('thumbnail')
                                ->image()
                                ->label('Foto Produk')
                                ->visibility('public')
                                ->disk('public')
                                ->directory('product-thumbnails'),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label('Tampilkan di Toko')
                                ->default(true),

                            Toggle::make('has_variant')
                                ->label('Produk memiliki variasi')
                                ->default(false)
                                ->live(),
                        ]),

                        /* SINGLE PRODUCT PRICE */
                        Section::make('Harga & Stok')
                            ->visible(fn(Get $get) => !$get('has_variant'))
                            ->description('Input harga dan stok untuk produk ini')
                            ->schema([
                                Grid::make(4)->schema([
                                    TextInput::make('single.cost_price')
                                        ->label('Harga Modal')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->required()
                                        ->minValue(0)
                                        ->default(0),

                                    TextInput::make('single.sale_price')
                                        ->label('Harga Jual')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->required()
                                        ->minValue(0)
                                        ->default(0),

                                    TextInput::make('single.stock')
                                        ->label('Stok')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->default(0),

                                    TextInput::make('single.sku')
                                        ->label('SKU')
                                        ->disabled()
                                        ->dehydrated()
                                        ->extraAttributes(['class' => 'bg-gray-50'])
                                        ->default(
                                            fn(Get $get) =>
                                            strtoupper(Str::slug($get('name') ?? 'PROD'))
                                        )
                                        ->helperText('Otomatis dibuat dari nama produk'),
                                ]),
                            ])
                            ->collapsible(),
                    ])
                    ->icon('heroicon-o-shopping-bag'),

                /* ======================================================
                 * STEP 2 — ATRIBUT (MULTI VARIANT)
                 * ====================================================== */
                Step::make('Variasi')
                    ->visible(fn(Get $get) => $get('has_variant'))
                    ->schema([
                        Repeater::make('attributeOptions')
                            ->label('Pengaturan Variasi')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('attribute_id')
                                        ->label('Jenis Atribut')
                                        ->options(Attribute::pluck('name', 'id'))
                                        ->searchable()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label('Nama Atribut Baru')
                                                ->required()
                                                ->unique(Attribute::class, 'name')
                                        ])
                                        ->createOptionUsing(fn(array $data) => Attribute::create($data)->id)
                                        ->live()
                                        ->required(),

                                    Select::make('values')
                                        ->label('Pilihan Opsi')
                                        ->multiple()
                                        ->options(function (Get $get) {
                                            $attributeId = $get('attribute_id');
                                            if (!$attributeId) return [];
                                            return AttributeValue::where('attribute_id', $attributeId)->pluck('value', 'id');
                                        })
                                        ->preload()
                                        ->searchable()
                                        ->disabled(fn(Get $get) => !$get('attribute_id')) // Kunci jika atribut belum dipilih
                                        ->required()
                                        // --- TAMBAHKAN BAGIAN INI ---
                                        ->createOptionForm([
                                            TextInput::make('value')
                                                ->label('Nilai Opsi Baru')
                                                ->required(),
                                        ])
                                        ->createOptionUsing(function (array $data, Get $get) {
                                            return AttributeValue::create([
                                                'attribute_id' => $get('attribute_id'), // Menghubungkan otomatis ke Jenis Atribut
                                                'value' => $data['value'],
                                            ])->id;
                                        })
                                        // ----------------------------
                                        ->live()
                                        ->afterStateUpdated(fn(Get $get, Set $set) => static::updateVariants($get, $set)),
                                ]),
                            ])
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => static::updateVariants($get, $set))
                            ->minItems(1)
                            ->maxItems(2)
                            ->addActionLabel('+ Tambah Variasi')
                            ->itemLabel(
                                fn(array $state): ?string =>
                                $state['attribute_id']
                                    ? Attribute::find($state['attribute_id'])?->name
                                    : 'Variasi Baru'
                            ),
                    ])
                    ->icon('heroicon-o-adjustments-horizontal'),

                /* ======================================================
                 * STEP 3 — MATRIKS PENJUALAN (HANYA UNTUK MULTI VARIANT)
                 * ====================================================== */
                Step::make('Matriks Penjualan')
                    ->visible(fn(Get $get) => (bool)$get('has_variant'))
                    ->schema([
                        Section::make('Detail Setiap Variasi')
                            ->schema([
                                Repeater::make('variants')
                                    ->label('Daftar Kombinasi Variasi')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->hintAction(
                                        Action::make('set_mass_data')
                                            ->label('Edit Massal')
                                            ->icon('heroicon-m-sparkles')
                                            ->color('warning')
                                            ->tooltip('Terapkan harga/stok yang sama ke semua variasi')
                                            ->form([
                                                Section::make()
                                                    ->description('Isi field yang ingin diterapkan ke semua variasi')
                                                    ->schema([
                                                        Grid::make(3)->schema([
                                                            TextInput::make('mass_cost_price')
                                                                ->label('Harga Modal')
                                                                ->numeric()
                                                                ->prefix('Rp')
                                                                ->minValue(0),

                                                            TextInput::make('mass_sale_price')
                                                                ->label('Harga Jual')
                                                                ->numeric()
                                                                ->prefix('Rp')
                                                                ->minValue(0),

                                                            TextInput::make('mass_stock')
                                                                ->label('Stok')
                                                                ->numeric()
                                                                ->minValue(0),
                                                        ]),
                                                    ]),
                                            ])
                                            ->action(function (array $data, Set $set, Get $get) {
                                                $variants = $get('variants');
                                                if (empty($variants)) return;

                                                $updatedVariants = collect($variants)->map(function ($variant) use ($data) {
                                                    if (!empty($data['mass_cost_price']))
                                                        $variant['cost_price'] = $data['mass_cost_price'];

                                                    if (!empty($data['mass_sale_price']))
                                                        $variant['sale_price'] = $data['mass_sale_price'];

                                                    if ($data['mass_stock'] !== null && $data['mass_stock'] !== '')
                                                        $variant['stock'] = $data['mass_stock'];

                                                    return $variant;
                                                })->toArray();

                                                $set('variants', $updatedVariants);
                                            })
                                            ->successNotificationTitle('Data berhasil diterapkan ke semua variasi')
                                    )
                                    ->schema([
                                        Grid::make(5)->schema([
                                            TextInput::make('variant_name')
                                                ->label('Nama Varian')
                                                ->disabled()
                                                ->dehydrated()
                                                ->extraAttributes(['class' => 'bg-gray-50 font-semibold'])
                                                ->columnSpan(2),

                                            TextInput::make('cost_price')
                                                ->label('Harga Modal')
                                                ->numeric()
                                                ->prefix('Rp')
                                                ->required()
                                                ->minValue(0)
                                                ->default(0),

                                            TextInput::make('sale_price')
                                                ->label('Harga Jual')
                                                ->numeric()
                                                ->prefix('Rp')
                                                ->required()
                                                ->minValue(0)
                                                ->default(0),

                                            TextInput::make('stock')
                                                ->label('Stok')
                                                ->numeric()
                                                ->required()
                                                ->minValue(0)
                                                ->default(0),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('sku')
                                                ->label('SKU')
                                                ->disabled()
                                                ->dehydrated()
                                                ->extraAttributes(['class' => 'bg-gray-50']),

                                            FileUpload::make('image')
                                                ->label('Foto Varian (Opsional)')
                                                ->image()
                                                ->directory('product-variants')
                                                ->disk('public')
                                                ->visibility('public')
                                                ->maxSize(2048)
                                                ->imageEditor(),
                                        ]),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        $state['variant_name'] ?? 'Variasi'
                                    )
                                    ->helperText('Nama dan SKU varian dibuat otomatis dari kombinasi atribut'),
                            ]),
                    ])
                    ->icon('heroicon-o-currency-dollar')
                    ->completedIcon('heroicon-o-check-circle'),

            ])
                ->columnSpanFull()
                ->skippable(true)
                ->persistStepInQueryString(),
        ]);
    }

    /* ======================================================
     * GENERATE VARIANT COMBINATIONS
     * Method untuk membuat kombinasi otomatis dari atribut
     * ====================================================== */
    protected static function updateVariants(Get $get, Set $set): void
    {
        $options = $get('attributeOptions');

        // Reset jika tidak ada attributeOptions
        if (!$options) {
            $set('variants', []);
            return;
        }

        // Kumpulkan semua nilai atribut ke dalam matrix
        $matrix = [];
        foreach ($options as $opt) {
            if (!empty($opt['values'])) {
                $matrix[] = AttributeValue::whereIn('id', $opt['values'])
                    ->pluck('value')
                    ->toArray();
            }
        }

        // Reset jika matrix kosong
        if (empty($matrix)) {
            $set('variants', []);
            return;
        }

        // Generate kombinasi dari matrix
        $combinations = Product::generateCombinations($matrix);
        $currentVariants = collect($get('variants') ?? []);
        $productName = $get('name') ?? 'PRODUCT'; // Fallback jika nama produk kosong
        $newVariants = [];

        foreach ($combinations as $combo) {
            // ✅ Format nama varian: "Merah / XL / Katun"
            $variantName = implode(' / ', $combo);

            // ✅ Generate SKU otomatis: NAMA-PRODUK-VARIAN
            $autoSku = strtoupper(Str::slug($productName . '-' . $variantName));

            // Cari apakah kombinasi ini sudah ada (untuk preserve data yang sudah diinput)
            $existing = $currentVariants->firstWhere('variant_name', $variantName);

            if ($existing) {
                // ✅ PERTAHANKAN data yang sudah diinput user
                // Tapi UPDATE SKU jika nama produk berubah
                $existing['sku'] = $autoSku;
                $existing['variant_name'] = $variantName; // Update juga nama jika ada perubahan
                $newVariants[] = $existing;
            } else {
                // ✅ BUAT kombinasi baru dengan nama dan SKU otomatis
                $newVariants[] = [
                    'variant_name' => $variantName,
                    'cost_price' => 0,
                    'sale_price' => 0,
                    'stock' => 0,
                    'sku' => $autoSku,
                    'image' => null,
                ];
            }
        }

        // Set variants baru ke form
        $set('variants', $newVariants);
    }
}
