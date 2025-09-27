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
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('variant_name')->nullable(); // contoh: Hitam, Merah (boleh null)
            $table->string('size_name')->nullable();    // contoh: L, XL, 38, 39 (boleh null)
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();        // gambar khusus untuk kombinasi ini
            $table->timestamps();
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
