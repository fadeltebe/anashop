<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'owner',
        'code',
        'name',
        'slug',
        'description',
        'thumbnail',
        'has_variant', // Kolom baru untuk kontrol UI
        'rating',
        'rating_count',
        'total_sales',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // AUTO PRODUCT CODE (Disesuaikan dari CAT ke PRD)
            if (empty($product->code)) {
                $lastNumber = static::select(
                    DB::raw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_number")
                )->value('max_number');

                $nextNumber = ($lastNumber ?? 0) + 1;
                $product->code = 'PRD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            // AUTO SLUG & SLUG UNIQUE
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            $originalSlug = $product->slug;
            $count = 1;
            while (static::where('slug', $product->slug)->exists()) {
                $product->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });

        static::updating(function ($product) {
            // Mengunci kode agar tidak berubah saat update
            $product->code = $product->getOriginal('code');
        });
    }

    // ==========================================
    // RELATIONS
    // ==========================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Menggantikan items(). Sekarang merujuk ke tabel product_variants.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(Banner::class, 'banner_product');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(ProductCollection::class, 'product_collection_items', 'product_id', 'collection_id')
            ->withPivot('sort_order', 'is_active')
            ->withTimestamps()
            ->orderBy('product_collection_items.sort_order');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)
                ->orWhere('code', 'like', $term);
        });
    }

    // Tambahan helper untuk mendapatkan harga terendah dari varian
    public function getPriceRangeAttribute()
    {
        $min = $this->variants()->min('price');
        $max = $this->variants()->max('price');

        if ($min == $max) return $min;
        return "{$min} - {$max}";
    }

    public function attributeOptions(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class);
    }

    public static function generateCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $index => $values) {
            $tmp = [];
            foreach ($result as $resultItem) {
                foreach ($values as $value) {
                    $tmp[] = array_merge($resultItem, [$index => $value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
