<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        // 9:00 〜 17:00 の範囲で30分単位で開始
        $startHour = $this->faker->numberBetween(9, 16);
        $startMinute = $this->faker->randomElement([0, 30]);
        $duration = $this->faker->randomElement([30, 60, 90]); // 分単位

        $startTime = sprintf('%02d:%02d', $startHour, $startMinute);
        $startMinutes = $startHour * 60 + $startMinute;
        $endMinutes = $startMinutes + $duration;

        // 18:00を超えないよう調整
        if ($endMinutes > 18 * 60) {
            $endMinutes = 18 * 60;
            $startMinutes = $endMinutes - $duration;
            $startTime = sprintf('%02d:%02d', intdiv($startMinutes, 60), $startMinutes % 60);
        }

        $endTime = sprintf('%02d:%02d', intdiv($endMinutes, 60), $endMinutes % 60);

        return [
            'customer_id' => rand(1, 10),
            'menu_id' => rand(1, 5),
            'staff_id' => rand(1, 3),
            'reserved_date' => $this->faker->date(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $duration,
            'memo' => $this->faker->sentence,
        ];
    }
}
