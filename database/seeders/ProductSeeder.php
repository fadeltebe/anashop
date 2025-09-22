<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'code' => 'BB001', 'name' => 'Baju Bayi Lengan Panjang', 'slug' => 'baju-bayi-lengan-panjang', 'price' => 80000, 'discount_price' => 70000, 'stock' => 50, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['bayi1.jpg', 'bayi2.jpg'])],
            ['category_id' => 2, 'code' => 'BA001', 'name' => 'Kaos Anak Perempuan', 'slug' => 'kaos-anak-perempuan', 'price' => 90000, 'discount_price' => 85000, 'stock' => 40, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['anak1.jpg', 'anak2.jpg'])],
            ['category_id' => 3, 'code' => 'BR001', 'name' => 'Kemeja Remaja Laki-laki', 'slug' => 'kemeja-remaja-laki', 'price' => 120000, 'discount_price' => 110000, 'stock' => 30, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['remaja1.jpg', 'remaja2.jpg'])],
            ['category_id' => 4, 'code' => 'BD001', 'name' => 'Kemeja Dewasa Pria', 'slug' => 'kemeja-dewasa-pria', 'price' => 150000, 'discount_price' => 140000, 'stock' => 25, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['dewasa1.jpg', 'dewasa2.jpg'])],
            ['category_id' => 5, 'code' => 'HJ001', 'name' => 'Hijab Segi Empat Motif', 'slug' => 'hijab-segi-empat-motif', 'price' => 50000, 'discount_price' => 45000, 'stock' => 60, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['hijab1.jpg', 'hijab2.jpg'])],
            ['category_id' => 6, 'code' => 'DM001', 'name' => 'Dress Muslimah Panjang', 'slug' => 'dress-muslimah-panjang', 'price' => 250000, 'discount_price' => 230000, 'stock' => 20, 'total_sales' => 0, 'thumbnail' => null, 'photos' => json_encode(['dress1.jpg', 'dress2.jpg'])],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Hitung total produk per kategori
        foreach (Category::all() as $category) {
            $category->total_products = Product::where('category_id', $category->id)->count();
            $category->save();
        }
    }
}
