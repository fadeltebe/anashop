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
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('sku')->unique();

            $table->json('attributes')->nullable();

            // HARGA
            $table->decimal('cost_price', 12, 2);                 // MODAL
            $table->decimal('sale_price', 12, 2);                 // NORMAL
            $table->decimal('discount_price', 12, 2)->nullable(); // SETELAH DISKON

            // STOK & LOGISTIK
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight')->default(0);

            // STATISTIK
            $table->unsignedInteger('total_sales')->default(0);

            $table->string('variant_1_value')->nullable(); // contoh: Merah
            $table->string('variant_2_value')->nullable(); // contoh: L
            $table->string('image')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
