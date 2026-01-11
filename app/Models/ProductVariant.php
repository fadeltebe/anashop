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

    protected $fillable = [
        'product_id',
        'sku',
        'variant_name', // Nama gabungan: Merah, L
        'price',
        'cost_price',
        'stock',
        'image', // Foto spesifik SKU jika berbeda dari foto warna
        'weight_grams',
    ];

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
}
