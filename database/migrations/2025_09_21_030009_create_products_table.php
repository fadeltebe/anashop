<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade'); // If category is deleted, delete its products

            $table->string('code')->unique();       // Product code / SKU
            $table->string('name');                 // Product name
            $table->string('slug')->unique();       // SEO friendly URL
            $table->unsignedInteger('price');       // Price in smallest unit (e.g. cents)
            $table->unsignedInteger('discount_price');       // Price in smallest unit (e.g. cents)
            $table->unsignedInteger('stock');       // Stock quantity
            $table->unsignedInteger('total_sales')->default(0); // Track sales count

            $table->string('thumbnail')->nullable(); // Main image
            $table->json('photos')->nullable();      // Gallery images (multiple)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
