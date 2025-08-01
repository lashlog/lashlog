<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $shopId = 1;
        $staff = Staff::inRandomOrder()->first();
        $staffId = $staff?->id ?? 1;
        $customerId = Customer::inRandomOrder()->first()?->id ?? 1;
        $menuId = Menu::inRandomOrder()->first()?->id ?? 1;

        // 営業時間とスロット
        $slots = $this->generateTimeSlots('09:00', '17:00'); // 30分単位
        $used = Reservation::where('staff_id', $staffId)->pluck('start_time')->toArray();

        // 未使用スロットを探す
        $availableSlots = array_filter($slots, fn($slot) => !in_array($slot, $used));

        if (empty($availableSlots)) {
            // 空きスロットがない場合は 9:00 に固定（テスト目的）
            $start = Carbon::today()->setTime(9, 0);
        } else {
            $startStr = $this->faker->randomElement($availableSlots);
            $start = Carbon::today()->setTimeFromTimeString($startStr);
        }

        $duration = 30;
        $end = (clone $start)->addMinutes($duration);

        return [
            'shop_id' => $shopId,
            'staff_id' => $staffId,
            'customer_id' => $customerId,
            'menu_id' => $menuId,
            'reserved_date' => $start->format('Y-m-d'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'price' => $this->faker->numberBetween(1000, 10000),
            'memo' => $this->faker->realText(20),
        ];
    }

    private function generateTimeSlots(string $start = '09:00', string $end = '17:00', int $interval = 30): array
    {
        $slots = [];
        $start = Carbon::createFromTimeString($start);
        $end = Carbon::createFromTimeString($end);

        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($interval);
        }

        return $slots;
    }
}
