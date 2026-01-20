<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle payment status change
     * Decrement stock hanya saat payment_status berubah jadi 'paid' (sesuaikan dengan enum Anda)
     */
    public function updated(Transaction $transaction)
    {
        // ✅ Cek apakah payment_status berubah ke 'paid' (ubah ke 'paid' jika enum Anda berbeda)
        if ($transaction->isDirty('payment_status') && $transaction->payment_status === 'paid') {  // Sesuaikan status enum: 'paid' sesuai TransactionForm
            $this->decrementStock($transaction);
        }

        // ✅ Jika transaksi dibatalkan/gagal, restore stock
        if ($transaction->isDirty('status') && in_array($transaction->status, ['Batal', 'failed'])) {  // Sesuaikan status enum: 'Batal', 'failed' sesuai TransactionForm
            $this->restoreStock($transaction);
        }
    }

    /**
     * Decrement stock saat payment berhasil
     */
    private function decrementStock(Transaction $transaction)
    {
        foreach ($transaction->items as $item) {
            try {
                $variant = ProductVariant::lockForUpdate()->find($item->variant_id);

                if ($variant && $variant->stock >= $item->quantity) {
                    $variant->decrement('stock', $item->quantity);

                    Log::info('Stock decremented', [
                        'transaction_id' => $transaction->id,
                        'variant_id' => $variant->id,
                        'quantity' => $item->quantity,
                        'remaining_stock' => $variant->fresh()->stock
                    ]);
                } else {
                    // Jika stok tidak cukup, tandai transaksi sebagai problem
                    Log::warning('Insufficient stock when processing payment', [
                        'transaction_id' => $transaction->id,
                        'variant_id' => $item->variant_id,
                        'required' => $item->quantity,
                        'available' => $variant?->stock ?? 0
                    ]);

                    // Opsional: Update status transaksi atau kirim notifikasi
                    // $transaction->update(['status' => 'Problem']);
                }
            } catch (\Exception $e) {
                Log::error('Error decrementing stock: ' . $e->getMessage(), [
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id
                ]);
            }
        }
    }

    /**
     * Restore stock jika transaksi dibatalkan
     */
    private function restoreStock(Transaction $transaction)
    {
        // Hanya restore jika sebelumnya sudah paid
        if ($transaction->getOriginal('payment_status') === 'paid') {  // Sesuaikan
            foreach ($transaction->items as $item) {
                try {
                    $variant = ProductVariant::find($item->variant_id);

                    if ($variant) {
                        $variant->increment('stock', $item->quantity);

                        Log::info('Stock restored', [
                            'transaction_id' => $transaction->id,
                            'variant_id' => $variant->id,
                            'quantity' => $item->quantity
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error restoring stock: ' . $e->getMessage());
                }
            }
        }
    }
}
