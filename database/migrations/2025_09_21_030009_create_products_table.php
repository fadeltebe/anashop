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

            // RELATION
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            // IDENTITAS
            $table->string('owner');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('slug')->unique();

            // KONTEN
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();

            // FLAG VARIASI
            // Penting: Jika false, produk hanya punya 1 variant (default).
            // Jika true, produk punya banyak variant hasil kombinasi attribute_values.
            $table->boolean('has_variant')->default(false);

            // RATING & STATS
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('total_sales')->default(0);

            // STATUS
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Matikan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Hapus tabel
        Schema::dropIfExists('products');

        // 3. Hidupkan kembali pengecekan
        Schema::enableForeignKeyConstraints();
    }
};
