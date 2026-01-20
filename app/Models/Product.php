<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'has_variant',
        'rating',
        'rating_count',
        'total_sales',
        'is_active',
    ];

    protected $casts = [
        'has_variant' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // 1. AUTO PRODUCT CODE
            if (empty($product->code)) {
                $lastNumber = static::withTrashed()->select(
                    DB::raw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_number")
                )->value('max_number');

                $nextNumber = ($lastNumber ?? 0) + 1;
                $product->code = 'PRD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            // 2. AUTO SLUG & UNIQUE CHECK
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            $originalSlug = $product->slug;
            $count = 1;
            while (static::withTrashed()->where('slug', $product->slug)->exists()) {
                $product->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($product) {
            $product->code = $product->getOriginal('code');

            // 3. LOGIKA MEMBERSIHKAN VARIAN (Cleanup)
            // Jika user mengubah produk dari BERVARIAN menjadi TUNGGAL (has_variant: true -> false)
            if ($product->isDirty('has_variant') && !$product->has_variant) {
                // Hapus semua varian kecuali yang nantinya akan jadi 'Default'
                // Ini mencegah tabel variants penuh dengan sampah kombinasi lama
                $product->variants()->where('variant_name', '!=', 'Default')->delete();
            }
        });

        // 4. SOFT DELETE RELATIONS
        static::deleting(function ($product) {
            if ($product->isForceDeleting()) {
                $product->variants()->forceDelete();
                $product->attributeOptions()->forceDelete();
            } else {
                $product->variants()->delete();
            }
        });
    }

    /**
     * Helper untuk Generate Kombinasi (Cartesian Product)
     */
    public static function generateCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    /**
     * RELATIONS
     */


    /**
     * ACCESSORS (Penting untuk Transaksi & Frontend)
     */

    // Mengambil range harga untuk ditampilkan di katalog
    public function getPriceRangeAttribute()
    {
        $min = $this->variants()->min('price');
        $max = $this->variants()->max('price');

        if ($min == $max) return "Rp " . number_format($min, 0, ',', '.');
        return "Rp " . number_format($min, 0, ',', '.') . " - Rp " . number_format($max, 0, ',', '.');
    }

    // Mengambil total stok dari semua varian
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants()->sum('stock');
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

    public function getPrimaryVariantAttribute()
    {
        if ($this->has_variant) {
            // Multi variant → ambil yang termurah
            return $this->variants()
                ->orderBy('sale_price', 'asc')
                ->first();
        }

        // Single product → ambil default variant
        return $this->variants()
            ->where('variant_name', 'Default')
            ->first();
    }

    public function getDisplayPriceAttribute(): string
    {
        $variant = $this->primary_variant;

        if (! $variant) {
            return 'Rp -';
        }

        return 'Rp ' . number_format($variant->sale_price, 0, ',', '.');
    }


    public function attributeOptions(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class);
    }
}
