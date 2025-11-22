<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $collections = [
            [
                'name' => 'Live Produk',
                'slug' => 'live-produk',
                'icon' => '🔴',
                'description' => 'Produk yang sedang live dan trending saat ini',
                'is_active' => true,
                'start_at' => null,
                'end_at' => null,
                'sort_order' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Flash Sale',
                'slug' => 'flash-sale',
                'icon' => '🔥',
                'description' => 'Diskon besar-besaran dalam waktu terbatas',
                'is_active' => true,
                'start_at' => Carbon::create(2025, 1, 1, 0, 0, 0),
                'end_at' => Carbon::create(2025, 1, 2, 23, 59, 59),
                'sort_order' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Produk Unggulan',
                'slug' => 'produk-unggulan',
                'icon' => '⭐',
                'description' => 'Produk pilihan terbaik dan paling diminati',
                'is_active' => true,
                'start_at' => null,
                'end_at' => null,
                'sort_order' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('product_collections')->insert($collections);
    }
}
