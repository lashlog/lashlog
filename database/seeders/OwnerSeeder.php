<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        Owner::create([
            'name' => 'Lash Log 運営',
            'company_name' => 'Lash Log Inc.',
            'email' => 'owner@lashlog.test',
            'phone' => '0120-000-000',
        ]);
    }
}
