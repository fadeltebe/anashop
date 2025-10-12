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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary key transaksi
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); // Relasi ke tabel customers, nullable jika transaksi walk-in
            $table->date('transaction_date'); // Tanggal transaksi dilakukan
            $table->decimal('total', 15, 2)->default(0); // Jumlah total dari semua item sebelum diskon dan biaya tambahan
            $table->decimal('discount', 15, 2)->default(0); // Potongan harga yang diberikan pada transaksi (nominal)
            $table->decimal('additional_fee', 15, 2)->default(0); // Biaya tambahan seperti ongkos kirim atau packaging
            $table->decimal('grand_total', 15, 2)->default(0); // Total akhir yang harus dibayar: total - discount + additional_fee
            $table->string('status')->default('pending'); // Status transaksi: pending, paid, cancelled
            $table->text('note')->nullable()->after('additional_fee');
            $table->text('admin_note')->nullable()->after('note');
            $table->timestamps(); // created_at dan updated_at
            $table->softDeletes(); // Untuk mengaktifkan soft deletes

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
