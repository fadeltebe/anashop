<?php

namespace App\Models;

use Illuminate\Support\Str;
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

        // Buat slug unique saat creating
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Pastikan slug unique
            $originalSlug = $category->slug;
            $count = 1;

            while (static::where('slug', $category->slug)->exists()) {
                $category->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
