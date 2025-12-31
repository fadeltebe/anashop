<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'slug',
        'description',
        'is_active',
    ];

    // Relasi ke produk (many-to-many)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'banner_product', 'banner_id', 'product_id');
    }
}
