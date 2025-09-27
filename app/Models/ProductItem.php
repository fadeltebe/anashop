<?php

// app/Models/ProductItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant', 'size', 'price', 'stock', 'sku', 'total_sales', 'image'];

    // protected static function booted()
    // {
    //     static::saved(function ($item) {
    //         $item->product->update([
    //             'total_sales' => $item->product->items()->sum('total_sales'),
    //         ]);
    //     });

    //     static::deleted(function ($item) {
    //         $item->product->update([
    //             'total_sales' => $item->product->items()->sum('total_sales'),
    //         ]);
    //     });
    // }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        // Setiap kali ProductItem dibuat, diupdate, atau dihapus
        static::saved(fn($item) => $item->syncProductTotalSales());
        static::deleted(fn($item) => $item->syncProductTotalSales());
    }

    protected function syncProductTotalSales(): void
    {
        if ($this->product) {
            $this->product->update([
                'total_sales' => $this->product->items()->sum('total_sales'),
            ]);
        }
    }
}
