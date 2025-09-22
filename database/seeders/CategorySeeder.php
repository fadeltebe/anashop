<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Baju Bayi', 'slug' => 'baju-bayi', 'is_active' => true, 'description' => 'Pakaian bayi', 'total_products' => 0],
            ['name' => 'Baju Anak', 'slug' => 'baju-anak', 'is_active' => true, 'description' => 'Pakaian anak-anak', 'total_products' => 0],
            ['name' => 'Baju Remaja', 'slug' => 'baju-remaja', 'is_active' => true, 'description' => 'Pakaian remaja', 'total_products' => 0],
            ['name' => 'Baju Dewasa', 'slug' => 'baju-dewasa', 'is_active' => true, 'description' => 'Pakaian dewasa', 'total_products' => 0],
            ['name' => 'Hijab', 'slug' => 'hijab', 'is_active' => true, 'description' => 'Berbagai model hijab', 'total_products' => 0],
            ['name' => 'Dress Muslimah', 'slug' => 'dress-muslimah', 'is_active' => true, 'description' => 'Dress muslimah modern', 'total_products' => 0],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
