<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'customer_id',
        'payment_status',
        'status',
        'total',
        'discount',
        'additional_fee',
        'grand_total',
        'transaction_code',
        'payment_method',
        'transaction_date',
        'note',
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
