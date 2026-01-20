<?php

namespace App\Filament\Resources\Products\Pages;

use App\Models\ProductVariant;
use App\Models\ProductAttributeOption;
use App\Models\VariantAttributeValue;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Products\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan boolean tersimpan
        $data['has_variant'] = (bool) ($data['has_variant'] ?? false);
        return $data;
    }

    protected function afterCreate(): void
    {
        $data    = $this->data;
        $product = $this->record;

        /* =====================================================
         * SINGLE PRODUCT
         * ===================================================== */
        if (! $product->has_variant && isset($data['single'])) {
            ProductVariant::create([
                'product_id'   => $product->id,
                'variant_name' => 'Default',
                'cost_price'   => (float) $data['single']['cost_price'],
                'sale_price'   => (float) $data['single']['sale_price'],
                'stock'        => (int) $data['single']['stock'],
                'sku'          => $data['single']['sku'],
            ]);

            return;
        }

        /* =====================================================
         * MULTI VARIANT
         * ===================================================== */
        if ($product->has_variant) {
            try {
                /* ---------------------------------------------
                 * 1. SIMPAN ATTRIBUTE OPTIONS
                 * --------------------------------------------- */
                if (! empty($data['attributeOptions'])) {
                    foreach ($data['attributeOptions'] as $opt) {
                        ProductAttributeOption::create([
                            'product_id'   => $product->id,
                            'attribute_id' => $opt['attribute_id'],
                            'values'       => $opt['values'], // array → json
                        ]);
                    }
                }

                /* ---------------------------------------------
                 * 2. SIMPAN VARIANT + PIVOT
                 * --------------------------------------------- */
                $successCount = 0;

                foreach ($data['variants'] as $variant) {

                    // handle image (FileUpload)
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

                    /* -----------------------------------------
                     * 3. SIMPAN VARIANT ↔ ATTRIBUTE VALUES
                     * ----------------------------------------- */
                    foreach ($data['attributeOptions'] as $opt) {
                        foreach ($opt['values'] as $valueId) {
                            VariantAttributeValue::create([
                                'product_variant_id' => $createdVariant->id,
                                'attribute_value_id' => $valueId,
                            ]);
                        }
                    }

                    $successCount++;
                }

                Notification::make()
                    ->success()
                    ->title('Produk berhasil dibuat')
                    ->body($successCount . ' variasi berhasil disimpan.')
                    ->send();
            } catch (\Throwable $e) {
                Log::error('CREATE PRODUCT ERROR', [
                    'message' => $e->getMessage(),
                ]);

                Notification::make()
                    ->danger()
                    ->title('Gagal menyimpan produk')
                    ->body($e->getMessage())
                    ->send();
            }
        }
    }
}
