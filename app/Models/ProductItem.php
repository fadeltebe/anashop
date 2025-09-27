<?php

// app/Models/ProductItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_name', 'size_name', 'stock', 'image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
