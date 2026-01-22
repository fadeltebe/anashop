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
            $table->id();
            $table->string('transaction_code')->unique(); // ✅ Tambahkan
            $table->foreignId('customer_id')->constrained();
            $table->timestamp('transaction_date');

            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('additional_fee', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);

            $table->string('payment_method'); // ✅ Tambahkan
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid'); // ✅ Tambahkan
            $table->string('payment_proof')->nullable(); // ✅ Tambahkan
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps(); // created_at dan updated_at
            $table->softDeletes(); // Untuk mengaktifkan soft deletes

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::dropIfExists('transactions');
    }
};
