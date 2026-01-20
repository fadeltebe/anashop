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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();

            // PERBAIKAN: Tambahkan nullable() agar bisa menjalankan nullOnDelete()
            $table->foreignId('product_id')
                ->nullable() // Wajib ada agar 'set null' bekerja
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            // Snapshot data (Sudah benar, sangat bagus untuk histori transaksi)
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();

            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
        });
        Schema::dropIfExists('transaction_items');
    }
};
