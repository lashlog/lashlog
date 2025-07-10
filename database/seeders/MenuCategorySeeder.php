<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // エクステ
            ['name' => 'フラットラッシュ', 'type' => 'extension'],
            ['name' => 'Wフラット', 'type' => 'extension'],
            ['name' => 'ボリューム', 'type' => 'extension'],
            ['name' => 'バインドロック', 'type' => 'extension'],
            ['name' => 'シングルエクステ', 'type' => 'extension'],

            // マツパ
            ['name' => 'ラッシュリフト', 'type' => 'perm'],
            ['name' => 'パリジェンヌ', 'type' => 'perm'],

            // 特殊系
            ['name' => 'アンドヘルシー', 'type' => 'hybrid'],
        ];

        foreach ($categories as $category) {
            DB::table('menu_categories')->insert([
                'name'       => $category['name'],
                'type'       => $category['type'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
