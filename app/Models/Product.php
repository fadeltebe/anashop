<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner',
        'slug',
        'code',
        'category_id',
        'price',
        'discount_price',
        'description',
        'thumbnail',
        'photos', // JSON field
        'stock',
        'is_featured',
        'is_flash_sale',
        'is_published',
        'is_live',
        'weight',
        'rating',
        'rating_count',
        'total_sales',
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

    public function scopeLive($query)
    {
        return $query->where('is_live', true)->latest();
    }

    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)->latest();
    }

    public function scopeFeatured($query)
    {
        return $query->orderBy('total_sales', 'desc');
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

    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }
}
