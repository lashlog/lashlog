<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::create([
            'owner_id' => 1, // 必要に応じて存在するIDに変更
            'shop_id' => 1,  // 同上
            'name' => '管理者',
            'email' => 'admin@lashlog.test',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);
    }
}
