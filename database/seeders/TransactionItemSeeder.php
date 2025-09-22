<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;

class TransactionItemSeeder extends Seeder
{
    public function run(): void
    {
        $transactions = Transaction::all();

        foreach ($transactions as $transaction) {
            // contoh item untuk transaksi pertama
            $products = Product::take(2)->get(); // ambil 2 produk pertama
            foreach ($products as $product) {
                $item = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => $product->price,
                    'subtotal' => 2 * $product->price,
                ]);
            }

            // setelah menambahkan item, hitung total dan grand_total
            $transaction->calculateTotals();
        }
    }
}
