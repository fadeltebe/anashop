<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '081234567890', 'address' => 'Jakarta'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '081298765432', 'address' => 'Bandung'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
