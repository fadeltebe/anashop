<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /* ======================================================
     * RELATIONS
     * ====================================================== */

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /* ======================================================
     * ACCESSORS
     * Dynamic attributes untuk kemudahan akses
     * ====================================================== */

    /**
     * Get harga per unit dari variant
     */
    public function getPriceAttribute(): float
    {
        return $this->variant?->sale_price ?? 0;
    }

    /**
     * Get subtotal (price * quantity)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get available stock dari variant
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->variant?->stock ?? 0;
    }

    /**
     * Get variant name
     */
    public function getVariantNameAttribute(): string
    {
        return $this->variant?->variant_name ?? 'Default';
    }

    /**
     * Get product name
     */
    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? 'Unknown Product';
    }

    /**
     * Get product image
     */
    public function getImageUrlAttribute(): string
    {
        // Priority: variant image → product thumbnail → default
        if ($this->variant && $this->variant->image) {
            return asset('storage/' . $this->variant->image);
        }

        if ($this->product && $this->product->thumbnail) {
            return asset('storage/' . $this->product->thumbnail);
        }

        return asset('images/default-product.png');
    }

    /**
     * Check if item available (product & variant masih ada dan ada stok)
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->product
            && $this->variant
            && $this->variant->stock > 0
            && $this->product->is_active;
    }

    /**
     * Check if quantity melebihi stock
     */
    public function getIsOverstockAttribute(): bool
    {
        return $this->quantity > $this->available_stock;
    }

    /* ======================================================
     * SCOPES
     * ====================================================== */

    /**
     * Scope untuk cart items yang available
     */
    public function scopeAvailable($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->where('is_active', true);
        })->whereHas('variant', function ($q) {
            $q->where('stock', '>', 0);
        });
    }

    /**
     * Scope untuk cart items yang overstock
     */
    public function scopeOverstock($query)
    {
        return $query->whereRaw('quantity > (
            SELECT stock FROM product_variants 
            WHERE product_variants.id = cart_items.variant_id
        )');
    }
}
