<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'customer_id',      // ID customer terkait transaksi
        'transaction_date', // Tanggal transaksi
        'total',            // Total semua item sebelum diskon/biaya tambahan
        'discount',         // Potongan harga (nominal)
        'additional_fee',   // Biaya tambahan (delivery, packaging, dll)
        'grand_total',      // Total akhir setelah diskon dan biaya tambahan
        'status',           // Status transaksi (pending, paid, cancelled)
    ];

    /**
     * Relasi ke customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke item transaksi
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Hitung total dan grand_total berdasarkan item, diskon, dan biaya tambahan
     */
    public function calculateTotals()
    {
        // Total dari semua item
        $this->total = $this->items->sum(fn($item) => $item->subtotal);

        // Grand total = total - discount + additional_fee
        $this->grand_total = $this->total - $this->discount + $this->additional_fee;

        $this->save();
    }
}
