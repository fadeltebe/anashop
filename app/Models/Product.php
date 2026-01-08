<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'is_published',
        'weight',
        'rating',
        'rating_count',
        'total_sales',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // Pastikan slug unique dengan menambah counter jika duplikat
            $originalSlug = $product->slug;
            $count = 1;

            while (static::where('slug', $product->slug)->exists()) {
                $product->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });
    }

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

    public function productItems()
    {
        return $this->hasMany(ProductItem::class);
    }


    public function collections()
    {
        return $this->belongsToMany(ProductCollection::class, 'product_collection_items', 'product_id', 'collection_id')
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_collection_items.sort_order');
    }

    public function collectionItems()
    {
        return $this->hasMany(ProductCollectionItem::class, 'product_id');
    }
}
