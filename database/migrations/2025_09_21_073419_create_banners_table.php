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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul banner
            $table->string('image'); // Gambar banner
            $table->string('slug')->unique(); // Untuk URL klik banner
            $table->text('description')->nullable(); // Optional
            $table->boolean('is_active')->default(true); // Banner aktif
            $table->timestamps();
            $table->softDeletes(); // Untuk mengaktifkan soft deletes

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
