<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'store_name' => 'Ana Shop',
            'address'    => 'Jl. Contoh No.123, Jakarta',
            'phone'      => '08123456789',
            'email'      => 'support@anashop.com',
            'logo'       => 'settings/logo.png',
            'favicon'    => 'settings/favicon.ico',
            'facebook'   => 'https://facebook.com/anashop',
            'instagram'  => 'https://instagram.com/anashop',
            'whatsapp'   => 'https://wa.me/628123456789',
        ]);
    }
}
