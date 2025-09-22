<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Customer;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $transactions = [
            [
                'customer_id' => 1,
                'transaction_date' => Carbon::today(),
                'total' => 0, // nanti dihitung dari item
                'discount' => 10000,
                'additional_fee' => 5000,
                'grand_total' => 0, // nanti dihitung
                'status' => 'pending',
            ],
            [
                'customer_id' => 2,
                'transaction_date' => Carbon::today(),
                'total' => 0,
                'discount' => 0,
                'additional_fee' => 10000,
                'grand_total' => 0,
                'status' => 'pending',
            ],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }
    }
}
