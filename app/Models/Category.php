<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description',
        'icon',
    ];

    protected static function boot()
    {
        parent::boot();

        // =========================
        // CREATING
        // =========================
        static::creating(function ($category) {

            // =========================
            // AUTO CATEGORY CODE
            // =========================
            if (empty($category->code)) {

                $lastNumber = Category::select(
                    DB::raw("MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_number")
                )
                    ->value('max_number');

                $nextNumber = ($lastNumber ?? 0) + 1;

                $category->code = 'CAT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            // =========================
            // AUTO SLUG
            // =========================
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // =========================
            // SLUG UNIQUE
            // =========================
            $originalSlug = $category->slug;
            $count = 1;

            while (
                static::where('slug', $category->slug)->exists()
            ) {
                $category->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });

        // =========================
        // UPDATING (LOCK CODE)
        // =========================
        static::updating(function ($category) {
            $category->code = $category->getOriginal('code');
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
