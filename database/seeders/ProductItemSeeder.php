<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductItem;

class ProductItemSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Case 1: Produk tanpa varian & size
            if ($product->id % 4 === 1) {
                ProductItem::create([
                    'product_id'   => $product->id,
                    'variant' => null,
                    'size'    => null,
                    'sku'     => 'SKU-' . strtoupper(uniqid()),
                    'price'   => $product->price,
                    'stock'        => rand(5, 50),
                    'total_sales'  => rand(0, 20),
                    'image'        => 'default.jpg',
                ]);
            }

            // Case 2: Produk dengan varian saja
            elseif ($product->id % 4 === 2) {
                foreach (['Hitam', 'Putih'] as $variant) {
                    ProductItem::create([
                        'product_id'   => $product->id,
                        'variant' => $variant,
                        'size'    => null,
                        'sku'     => 'SKU-' . strtoupper(uniqid()),
                        'price'   => $product->price,
                        'stock'        => rand(5, 50),
                        'total_sales'  => rand(0, 20),
                        'image'        => strtolower($variant) . '.jpg',
                    ]);
                }
            }

            // Case 3: Produk dengan size saja
            elseif ($product->id % 4 === 3) {
                foreach (['M', 'L', 'XL'] as $size) {
                    ProductItem::create([
                        'product_id'   => $product->id,
                        'variant' => null,
                        'size'    => $size,
                        'sku'     => 'SKU-' . strtoupper(uniqid()),
                        'price'   => $product->price,
                        'stock'        => rand(5, 50),
                        'total_sales'  => rand(0, 20),
                        'image'        => strtolower($size) . '.jpg',
                    ]);
                }
            }

            // Case 4: Produk dengan varian + size
            else {
                foreach (['Hitam', 'Putih'] as $variant) {
                    foreach (['M', 'L'] as $size) {
                        ProductItem::create([
                            'product_id'   => $product->id,
                            'variant' => $variant,
                            'size'    => $size,
                            'sku'     => 'SKU-' . strtoupper(uniqid()),
                            'price'   => $product->price,
                            'stock'        => rand(5, 50),
                            'total_sales'  => rand(0, 20),
                            'image'        => strtolower($variant . '-' . $size) . '.jpg',
                        ]);
                    }
                }
            }
        }
    }
}
