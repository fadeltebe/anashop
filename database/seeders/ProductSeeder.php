<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definisikan kisaran harga dan rating
        $minPrice = 15000;
        $maxPrice = 100000;

        // Rating harus di atas 4.5
        $minRating = 4.6;
        $maxRating = 5.0;

        // Fungsi helper untuk menghasilkan rating desimal (misal 4.75)
        $generateDecimalRating = function () use ($minRating, $maxRating) {
            // Ubah ke integer, tambahkan sedikit variasi (misal 4600 hingga 5000), lalu bagi 1000
            $randomInt = rand($minRating * 100, $maxRating * 100);
            return number_format($randomInt / 100, 2);
        };

        // Fungsi helper untuk menghasilkan harga desimal dalam kelipatan 5000
        $generatePrice = function ($min, $max) {
            $price = rand(ceil($min / 500), floor($max / 500)) * 500;
            return number_format($price, 2, '.', '');
        };

        // Data awal produk yang disederhanakan dan disesuaikan harganya
        $products = [
            // Harga dalam kisaran 15rb - 100rb, Rating > 4.5
            [
                'category_id'       => 1,
                'code' => 'BB001',
                'name' => 'Baju Bayi Lengan Panjang',
                'slug' => 'baju-bayi-lengan-panjang',
                'price'             => $generatePrice(70000, 90000),
                'discount_price' => $generatePrice(65000, 85000),
                'stock' => 50,
                'rating' => 4.85,
                'is_featured'       => true,
                'is_flash_sale' => false,
            ],
            [
                'category_id'       => 2,
                'code' => 'BA001',
                'name' => 'Kaos Anak Perempuan',
                'slug' => 'kaos-anak-perempuan',
                'price'             => $generatePrice(30000, 40000),
                'discount_price' => $generatePrice(25000, 35000),
                'stock' => 40,
                'rating' => 4.90,
                'is_featured'       => false,
                'is_flash_sale' => true,
            ],
            [
                'category_id'       => 3,
                'code' => 'BR001',
                'name' => 'Kemeja Remaja Laki-laki',
                'slug' => 'kemeja-remaja-laki',
                'price'             => $generatePrice(80000, 100000),
                'discount_price' => $generatePrice(75000, 95000),
                'stock' => 30,
                'rating' => 4.60,
                'is_featured'       => false,
                'is_flash_sale' => false,
            ],
            [
                'category_id'       => 4,
                'code' => 'BD001',
                'name' => 'Kemeja Dewasa Pria',
                'slug' => 'kemeja-dewasa-pria',
                'price'             => $generatePrice(90000, 100000),
                'discount_price' => $generatePrice(85000, 95000),
                'stock' => 25,
                'rating' => 5.00,
                'is_featured'       => true,
                'is_flash_sale' => false,
            ],
            [
                'category_id'       => 5,
                'code' => 'HJ001',
                'name' => 'Hijab Segi Empat Motif',
                'slug' => 'hijab-segi-empat-motif',
                'price'             => $generatePrice(20000, 35000),
                'discount_price' => $generatePrice(15000, 30000),
                'stock' => 60,
                'rating' => 4.75,
                'is_featured'       => false,
                'is_flash_sale' => false,
            ],
            [
                'category_id'       => 6,
                'code' => 'DM001',
                'name' => 'Dress Muslimah Panjang',
                'slug' => 'dress-muslimah-panjang',
                'price'             => $generatePrice(80000, 95000),
                'discount_price' => $generatePrice(75000, 90000),
                'stock' => 20,
                'rating' => 4.80,
                'is_featured'       => false,
                'is_flash_sale' => true,
            ],
        ];

        // Masukkan data produk awal dengan nilai default yang lengkap
        foreach ($products as $productData) {
            Product::create(array_merge([
                'total_sales'       => rand(1, 50),
                'weight'            => rand(80, 400),
                'rating_count'      => rand(5, 20),
                'description'       => 'Deskripsi produk ini sangat bagus dan berkualitas tinggi.',
                'thumbnail'         => null,
                'photos'            => json_encode(['default_1.jpg', 'default_2.jpg']),
                'is_published'      => true,
                'is_live'           => true,
            ], $productData));
        }

        // Generate 24 produk dummy tambahan (total 30)
        for ($i = 7; $i <= 30; $i++) {
            $categoryId = ($i % 6) + 1;
            $name = "Produk Unggulan {$i}";
            $slug = Str::slug($name);

            // Tentukan status produk secara acak
            $isFeatured = ($i % 5 == 0);
            $isFlashSale = ($i % 7 == 0);

            // Generate harga dan diskon
            $randomPrice = $generatePrice($minPrice, $maxPrice);
            $randomDiscount = $generatePrice(10000, (int)$randomPrice - 5000); // Diskon minimal 5rb

            Product::create([
                'category_id'       => $categoryId,
                'code'              => 'PD' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name'              => $name,
                'slug'              => $slug,
                'price'             => $randomPrice,
                'discount_price'    => $randomDiscount,
                'stock'             => rand(10, 100),
                'total_sales'       => rand(1, 50),
                'weight'            => rand(50, 500),
                'rating'            => $generateDecimalRating(), // Rating desimal 4.60 - 5.00
                'rating_count'      => rand(1, 20),
                'description'       => "Deskripsi singkat untuk {$name}. Produk ini sangat direkomendasikan.",
                'thumbnail'         => null,
                'photos'            => json_encode(["dummy{$i}_1.jpg", "dummy{$i}_2.jpg"]),
                'is_published'      => true,
                'is_live'           => true,
                'is_featured'       => $isFeatured,
                'is_flash_sale'     => $isFlashSale,
            ]);
        }

        // Perbarui total produk per kategori (opsional)
        foreach (Category::all() as $category) {
            $category->total_products = Product::where('category_id', $category->id)->count();
            $category->save();
        }
    }
}
