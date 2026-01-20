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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('sku')->unique()->nullable(); // Contoh: BHN-RED-L
            $table->string('variant_name')->nullable(); // Opsional: Untuk nama varian spesifik (Merah - L)

            $table->decimal('cost_price', 15, 2)->default(0); // Harga spesifik varian
            $table->decimal('sale_price', 15, 2)->default(0); // Harga modal spesifik varian
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();

            // Tambahan info berat jika nanti diperlukan untuk ongkir
            $table->integer('weight_grams')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
