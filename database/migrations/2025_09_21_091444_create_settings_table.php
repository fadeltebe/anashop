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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Ana Shop');   // Nama toko
            $table->string('address')->nullable();               // Alamat toko
            $table->string('phone')->nullable();                 // Nomor telepon
            $table->string('email')->nullable();                 // Email toko
            $table->string('logo')->nullable();                  // Logo (path ke storage)
            $table->string('favicon')->nullable();               // Favicon
            $table->string('facebook')->nullable();              // Link media sosial
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
