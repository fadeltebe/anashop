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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();       // Category name
            $table->string('slug')->unique();       // For SEO-friendly URLs
            $table->boolean('is_active')->default(true); // Active/inactive toggle
            $table->unsignedInteger('total_products')->default(0);
            $table->string('description')->nullable();   // Optional description
            $table->string('icon')->nullable();     // Emoji or image path
            $table->timestamps();
            $table->softDeletes(); // Untuk mengaktifkan soft deletes

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
