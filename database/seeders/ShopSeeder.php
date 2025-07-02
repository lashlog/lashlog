<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Shop::create([
            'name' => 'LashLog 本店',
            'phone' => '000-0000-0000',
            'email' => 'shop@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
