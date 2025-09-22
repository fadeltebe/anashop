<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'slug',
        'price',
        'stock',
        'total_sales',
        'thumbnail',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array', // Cast JSON to array
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_product', 'product_id', 'banner_id');
    }

    public function scopeFlashSale($query)
    {
        return $query->whereColumn('discount_price', '<', 'price');
    }

    public function scopeFeatured($query)
    {
        return $query->orderBy('id', 'desc');
    }
    public function scopeRecommended($query)
    {
        return $query->latest();
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)
                ->orWhere('code', 'like', $term);
        });
    }
}
