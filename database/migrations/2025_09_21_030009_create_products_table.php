<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Kunci Asing ke Kategori
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');

            // Informasi Dasar & SEO
            $table->string('code')->unique();       // Product code / SKU
            $table->string('name');                 // Product name
            $table->string('slug')->unique();       // SEO friendly URL

            // Harga (Menggunakan DECIMAL untuk presisi mata uang)
            // 10 digit total, 2 di belakang koma (contoh: 99,999,999.99)
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable(); // Harga setelah diskon

            // Inventaris & Penjualan
            $table->unsignedInteger('stock');       // Kuantitas stok
            $table->unsignedInteger('total_sales')->default(0); // Hitungan penjualan
            $table->unsignedBigInteger('weight')->default(0); // Berat dalam gram

            // Rating
            // Menggunakan SmallInteger (0-50 atau 0-500) untuk menyimpan rating (misal: 4.5 disimpan sebagai 45 atau 450)
            $table->decimal('rating', 10, 2);
            $table->unsignedInteger('rating_count')->default(0); // Jumlah ulasan yang diterima

            // Konten & Media
            $table->text('description')->nullable(); // Deskripsi produk
            $table->string('thumbnail')->nullable(); // Gambar utama
            $table->json('photos')->nullable();      // Gambar galeri (format JSON)

            // Status Produk (Untuk kemudahan filtering di Dashboard/Livewire)
            $table->boolean('is_published')->default(true); // Produk dapat dilihat publik
            $table->boolean('is_live')->default(true);      // Produk aktif dijual
            $table->boolean('is_featured')->default(false);  // Produk unggulan
            $table->boolean('is_flash_sale')->default(false); // Produk yang sedang dalam flash sale
            $table->timestamps();
            $table->softDeletes(); // Untuk mengaktifkan soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        Schema::dropIfExists('products');
    }
};
