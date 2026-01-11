<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\AttributeValue;
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
        return $schema
            ->components([
                Wizard::make([
                    // STEP 1: INFORMASI UMUM
                    Wizard\Step::make('Informasi Umum')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('owner')
                                    ->options([
                                        'Uma Alawi' => 'Uma Alawi',
                                        'Mama Zahra' => 'Mama Zahra'
                                    ])
                                    ->default('Mama Zahra')
                                    ->required(),
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->label('Kategori')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                            TextInput::make('name')
                                ->label('Nama Produk')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->disabled()
                                ->dehydrated(),
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->columnSpanFull(),
                            FileUpload::make('thumbnail')
                                ->image()
                                ->directory('products'),
                        ]),

                    // STEP 2: PENGATURAN VARIASI
                    Wizard\Step::make('Atur Variasi')
                        ->schema([
                            Toggle::make('has_variant')
                                ->label('Produk ini memiliki variasi (Warna, Ukuran, dll)')
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (!$state) $set('variants', []);
                                }),

                            Repeater::make('attributeOptions')
                                ->relationship('attributeOptions')
                                ->label('Konfigurasi Atribut')
                                ->hidden(fn(Get $get) => ! $get('has_variant'))
                                ->live()
                                ->afterStateUpdated(fn(Get $get, Set $set) => static::updateVariants($get, $set))
                                ->schema([
                                    Grid::make(2)->schema([
                                        Select::make('attribute_id')
                                            ->label('Jenis Atribut')
                                            ->options(Attribute::pluck('name', 'id'))
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')->required(),
                                            ])
                                            ->createOptionUsing(fn(array $data) => Attribute::create($data)->id)
                                            ->live()
                                            ->required(),

                                        Select::make('values')
                                            ->label('Pilihan Opsi')
                                            ->multiple()
                                            ->options(
                                                fn(Get $get) =>
                                                AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('value', 'id')
                                            )
                                            ->createOptionForm([
                                                TextInput::make('value')->required(),
                                            ])
                                            ->createOptionUsing(
                                                fn(array $data, Get $get) =>
                                                AttributeValue::create([
                                                    'attribute_id' => $get('attribute_id'),
                                                    'value' => $data['value']
                                                ])->id
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(fn(Get $get, Set $set) => static::updateVariants($get, $set))
                                            ->required(),
                                    ]),
                                ])
                                ->itemLabel(
                                    fn(array $state): ?string =>
                                    isset($state['attribute_id'])
                                        ? Attribute::find($state['attribute_id'])?->name
                                        : 'Tambah Atribut'
                                ),
                        ]),

                    // STEP 3: MATRIKS PENJUALAN
                    Wizard\Step::make('Matriks Penjualan')
                        ->schema([
                            // REPEATER VARIAN (Otomatis Terisi)
                            Repeater::make('variants')
                                ->relationship('variants')
                                ->label('Daftar Kombinasi Variasi')
                                ->hidden(fn(Get $get) => ! $get('has_variant'))
                                ->addable(false)
                                ->deletable(false)
                                ->hintAction(
                                    Action::make('set_mass_data')
                                        ->label('Edit Massal Harga & Stok')
                                        ->icon('heroicon-m-sparkles')
                                        ->color('warning')
                                        ->form([
                                            Grid::make(3)->schema([
                                                TextInput::make('mass_cost_price')->label('Harga Modal')->numeric()->prefix('Rp'),
                                                TextInput::make('mass_price')->label('Harga Jual')->numeric()->prefix('Rp'),
                                                TextInput::make('mass_stock')->label('Stok')->numeric(),
                                            ])
                                        ])
                                        ->action(function (array $data, Set $set, Get $get) {
                                            $variants = $get('variants');
                                            if (empty($variants)) return;

                                            $updatedVariants = collect($variants)->map(function ($variant) use ($data) {
                                                if (!empty($data['mass_cost_price'])) $variant['cost_price'] = $data['mass_cost_price'];
                                                if (!empty($data['mass_price'])) $variant['price'] = $data['mass_price'];
                                                if ($data['mass_stock'] !== null && $data['mass_stock'] !== '') $variant['stock'] = $data['mass_stock'];
                                                return $variant;
                                            })->toArray();

                                            $set('variants', $updatedVariants);
                                        })
                                )
                                ->schema([
                                    Grid::make(4)->schema([
                                        TextInput::make('variant_name')->label('Varian')->readOnly()->extraAttributes(['class' => 'bg-gray-50']),
                                        TextInput::make('cost_price')->label('Harga Modal')->numeric()->prefix('Rp')->required(),
                                        TextInput::make('price')->label('Harga Jual')->numeric()->prefix('Rp')->required(),
                                        TextInput::make('stock')->label('Stok')->numeric()->required(),
                                    ]),
                                    Grid::make(2)->schema([
                                        TextInput::make('sku')->label('SKU')->required(),
                                        FileUpload::make('image')->label('Foto Varian')->image()->directory('product-variants'),
                                    ]),
                                ]),

                            // SECTION PRODUK TUNGGAL (Jika tidak ada variasi)
                            Section::make('Informasi Harga & Stok')
                                ->description('Diisi jika produk tidak memiliki variasi')
                                ->hidden(fn(Get $get) => $get('has_variant'))
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextInput::make('single_cost_price')->label('Harga Modal')->numeric()->prefix('Rp'),
                                        TextInput::make('single_price')->label('Harga Jual')->numeric()->prefix('Rp'),
                                        TextInput::make('single_stock')->label('Stok')->numeric(),
                                    ]),
                                    TextInput::make('single_sku')->label('SKU'),
                                ]),
                        ]),
                ])->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Tampilkan di Toko')
                    ->default(true),
            ]);
    }

    /**
     * Fungsi Helper untuk Sinkronisasi Variasi secara Otomatis
     */
    protected static function updateVariants(Get $get, Set $set)
    {
        $options = $get('attributeOptions');

        if (empty($options)) {
            $set('variants', []);
            return;
        }

        $matrix = [];
        foreach ($options as $option) {
            if (!empty($option['values'])) {
                // Ambil label teks dari database berdasarkan ID yang dipilih
                $matrix[] = AttributeValue::whereIn('id', $option['values'])
                    ->pluck('value')
                    ->toArray();
            }
        }

        if (empty($matrix)) {
            $set('variants', []);
            return;
        }

        // Hitung Cartesian Product
        $combinations = Product::generateCombinations($matrix);
        $existingVariants = $get('variants') ?? [];
        $newVariants = [];

        foreach ($combinations as $combo) {
            $name = implode(', ', $combo);

            // Cari data lama agar inputan harga user tidak hilang saat menambah atribut
            $existing = collect($existingVariants)->firstWhere('variant_name', $name);

            $newVariants[] = [
                'variant_name' => $name,
                'cost_price'   => $existing['cost_price'] ?? 0,
                'price'        => $existing['price'] ?? 0,
                'stock'        => $existing['stock'] ?? 0,
                'sku'          => $existing['sku'] ?? strtoupper(Str::slug(($get('name') ?? 'PROD') . '-' . $name)),
                'image'        => $existing['image'] ?? null,
            ];
        }

        $set('variants', $newVariants);
    }
}
