<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    // ❌ HAPUS BARIS INI:
    // public ?ProductVariant $activeVariant = null;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_name', // Nama gabungan: Merah / L
        'cost_price',
        'sale_price',
        'stock',
        'image', // Foto spesifik varian
        'weight_grams',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'weight_grams' => 'integer',
    ];

    /* ======================================================
     * RELATIONS
     * ====================================================== */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Atribut (Warna, Ukuran, dll)
     * Ini yang membuat kita tahu varian ini "Merah" dan "L"
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values')
            ->withTimestamps();
    }

    /* ======================================================
     * ACCESSORS
     * ====================================================== */

    /**
     * Check apakah variant available (ada stok)
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Format harga untuk display
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->sale_price, 0, ',', '.');
    }

    /**
     * Hitung margin profit
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->sale_price <= 0) {
            return 0;
        }

        return (($this->sale_price - $this->cost_price) / $this->sale_price) * 100;
    }
}
