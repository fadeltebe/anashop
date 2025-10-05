<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant',
        'size',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper: Get item total
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Helper: Get stock availability
    public function getAvailableStockAttribute()
    {
        if ($this->variant || $this->size) {
            $item = $this->product->items()
                ->where('variant', $this->variant)
                ->where('size', $this->size)
                ->first();
            return $item ? $item->stock : 0;
        }
        return $this->product->stock ?? 0;
    }
}
