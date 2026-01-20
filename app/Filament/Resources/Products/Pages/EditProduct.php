<?php

namespace App\Filament\Resources\Products\Pages;

use App\Models\ProductVariant;
use App\Models\ProductAttributeOption;
use App\Models\VariantAttributeValue;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Filament\Resources\Products\ProductResource;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $product = $this->record;

        if (! $product->has_variant) {
            $variant = $product->variants()->first();
            if ($variant) {
                $data['single'] = [
                    'cost_price' => $variant->cost_price,
                    'sale_price' => $variant->sale_price,
                    'stock'      => $variant->stock,
                    'sku'        => $variant->sku,
                ];
            }
        }

        if ($product->has_variant) {
            $data['attributeOptions'] = $product->attributeOptions
                ->map(fn($opt) => [
                    'attribute_id' => $opt->attribute_id,
                    'values'       => $opt->values ?? [],
                ])
                ->toArray();

            $data['variants'] = $product->variants->map(fn($variant) => [
                'variant_name' => $variant->variant_name,
                'cost_price'   => $variant->cost_price,
                'sale_price'   => $variant->sale_price,
                'stock'        => $variant->stock,
                'sku'          => $variant->sku,
                'image'        => $variant->image,
            ])->toArray();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $product = $this->record;
        $data    = $this->form->getState();

        try {
            if (! $product->has_variant && isset($data['single'])) {
                // 1. Bersihkan attribute options jika ada
                $product->attributeOptions()->delete();

                // 2. Gunakan updateOrCreate untuk variant Default
                ProductVariant::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'variant_name' => 'Default',
                    ],
                    [
                        'cost_price' => (float) $data['single']['cost_price'],
                        'sale_price' => (float) $data['single']['sale_price'],
                        'stock'      => (int) $data['single']['stock'],
                        'sku'        => $data['single']['sku'],
                        'deleted_at' => null, // Memastikan jika sebelumnya soft-deleted, sekarang aktif kembali
                    ]
                );

                // 3. Hapus permanen varian lain agar tidak sampah di DB
                $product->variants()->where('variant_name', '!=', 'Default')->forceDelete();
            } elseif ($product->has_variant) {

                // --- PROSES MULTI VARIANT ---

                // 1. Reset Attribute Options
                $product->attributeOptions()->delete();
                if (! empty($data['attributeOptions'])) {
                    foreach ($data['attributeOptions'] as $opt) {
                        ProductAttributeOption::create([
                            'product_id'   => $product->id,
                            'attribute_id' => $opt['attribute_id'],
                            'values'       => $opt['values'],
                        ]);
                    }
                }

                // 2. SOLUSI: Force Delete varian lama sebelum create baru 
                // Ini mencegah error SKU duplikat dengan data yang ada di trash
                $product->variants()->forceDelete();

                $successCount = 0;
                foreach ($data['variants'] as $variant) {
                    $imagePath = null;
                    if (! empty($variant['image'])) {
                        $imagePath = is_array($variant['image'])
                            ? $variant['image'][0] ?? null
                            : $variant['image'];
                    }

                    $createdVariant = ProductVariant::create([
                        'product_id'   => $product->id,
                        'variant_name' => $variant['variant_name'],
                        'cost_price'   => (float) ($variant['cost_price'] ?? 0),
                        'sale_price'   => (float) ($variant['sale_price'] ?? 0),
                        'stock'        => (int) ($variant['stock'] ?? 0),
                        'sku'          => $variant['sku'],
                        'image'        => $imagePath,
                    ]);

                    // Pivot ke attribute values (jika diperlukan oleh sistem Anda)
                    // Perhatikan: Pastikan data['attributeOptions'] sinkron dengan penamaan variant
                    $successCount++;
                }

                Notification::make()
                    ->success()
                    ->title('Produk berhasil diperbarui')
                    ->body($successCount . ' variasi berhasil disimpan.')
                    ->send();
            }
        } catch (\Throwable $e) {
            Log::error('EDIT PRODUCT ERROR: ' . $e->getMessage());
            Notification::make()->danger()->title('Gagal menyimpan')->body($e->getMessage())->send();
        }
    }
}
