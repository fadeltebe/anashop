<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'cost_price',
        'sale_price',
        'discount_price',
        'stock',
        'weight',
        'total_sales',
        'variant_1_value',
        'variant_2_value',
        'image',
        'is_active',

    ];

    protected $casts = [
        'attributes' => 'array',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // =========================
    // RELATION
    // =========================
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // =========================
    // MODEL EVENTS
    // =========================
    protected static function boot()
    {
        parent::boot();

        // =========================
        // CREATING
        // =========================
        static::creating(function ($item) {

            // =========================
            // AUTO SKU
            // =========================
            if (empty($item->sku)) {

                if (!$item->product || !$item->product->code) {
                    throw new \Exception('Product harus punya code untuk generate SKU');
                }

                $sku = 'SKU-' . $item->product->code;

                // Tambahkan atribut ke SKU (jika ada)
                if (!empty($item->attributes) && is_array($item->attributes)) {
                    $attrPart = collect($item->attributes)
                        ->map(fn($value) => strtoupper(substr($value, 0, 3)))
                        ->implode('-');

                    if ($attrPart) {
                        $sku .= '-' . $attrPart;
                    }
                }

                $item->sku = $sku;
            }
        });

        // =========================
        // UPDATING (LOCK SKU)
        // =========================
        static::updating(function ($item) {
            $item->sku = $item->getOriginal('sku');
        });

        // =========================
        // SYNC PRODUCT TOTAL SALES
        // =========================
        static::saved(fn($item) => $item->syncProductTotalSales());
        static::deleted(fn($item) => $item->syncProductTotalSales());
    }

    // =========================
    // BUSINESS HELPER
    // =========================
    public function syncProductTotalSales(): void
    {
        if ($this->product) {
            $this->product->updateQuietly([
                'total_sales' => $this->product->items()->sum('total_sales'),
            ]);
        }
    }

    // =========================
    // ACCESSOR: FINAL PRICE
    // =========================
    public function getFinalPriceAttribute(): float
    {
        if ($this->discount_price && $this->discount_price > 0) {
            return max($this->sale_price - $this->discount_price, 0);
        }

        return $this->sale_price;
    }

    // =========================
    // ACCESSOR: PROFIT
    // =========================
    public function getProfitAttribute(): float
    {
        return $this->final_price - $this->cost_price;
    }
}
