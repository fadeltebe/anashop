<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'value', 'image'];

    /**
     * Kembali ke parent attribute
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Relasi ke Varian Produk (Many-to-Many) melalui tabel pivot
     */
    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values');
    }
}
