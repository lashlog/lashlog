<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {

        $baseNames = [
            'フラットラッシュ120本',
            'ボリュームラッシュ300束',
            'まつ毛パーマ',
            'WフラットMAX',
        ];
        // ランダムにベース名を選ぶ
        $base = $this->faker->randomElement($baseNames);

        // ランダムな2桁の番号を付ける（例: フラットラッシュ120本-17）
        $name = $base . '-' . $this->faker->unique()->numberBetween(1, 99);
        return [
            'shop_id' => 1, // テスト用に固定でもOK
            'name' => $name,
            'menu_category_id' => 1, // 固定 or ランダムでもOK
            'duration_minutes' => $this->faker->randomElement([60, 75, 90]),
            'price' => $this->faker->randomElement([4980, 5500, 6500, 7500]),
            'is_active' => true,
        ];
    }
}
