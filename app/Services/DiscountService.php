<?php

namespace App\Services;

use App\Models\DiscountRule;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\ReservationSource;
use Carbon\Carbon;

class DiscountService
{
    /**
     * Calculate applicable discounts and final price.
     */
    public function calculate(int $shopId, ?int $customerId, int $menuId, string $reservedDate, ?int $reservationSourceId = null, ?string $couponCode = null): array
    {
        $rules = DiscountRule::where('shop_id', $shopId)->where('is_active', true)->get();
        $menu = Menu::findOrFail($menuId);
        $basePrice = $menu->price;

        $customer = $customerId ? Customer::find($customerId) : null;
        $lastReservationDate = null;
        if ($customer) {
            $last = $customer->reservations()->orderByDesc('reserved_date')->first();
            if ($last) {
                $lastReservationDate = new Carbon($last->reserved_date);
            }
        }
        $sourceName = null;
        if ($reservationSourceId) {
            $source = ReservationSource::find($reservationSourceId);
            $sourceName = $source?->name;
        }
        $reserved = Carbon::parse($reservedDate);

        $discounts = [];
        foreach ($rules as $rule) {
            if ($rule->target_menu_ids && !in_array($menuId, $rule->target_menu_ids)) {
                continue;
            }
            switch ($rule->condition_type) {
                case 'first_time':
                    if ($customer && $customer->reservations()->count() === 0) {
                        $discounts[] = $this->buildDiscount($rule, $basePrice);
                    }
                    break;
                case 'repeat_within_days':
                    if ($customer && $lastReservationDate) {
                        $days = (int) $rule->condition_value;
                        if ($reserved->diffInDays($lastReservationDate) <= $days) {
                            $discounts[] = $this->buildDiscount($rule, $basePrice);
                        }
                    }
                    break;
                case 'birthday_month':
                    if ($customer && $customer->birthday && Carbon::parse($customer->birthday)->month === $reserved->month) {
                        $discounts[] = $this->buildDiscount($rule, $basePrice);
                    }
                    break;
                case 'reservation_source':
                    if ($sourceName && $sourceName === $rule->condition_value) {
                        $discounts[] = $this->buildDiscount($rule, $basePrice);
                    }
                    break;
                case 'coupon':
                    if ($couponCode && $couponCode === $rule->condition_value) {
                        $discounts[] = $this->buildDiscount($rule, $basePrice);
                    }
                    break;
            }
        }
        $total = array_sum(array_column($discounts, 'amount'));
        $final = max($basePrice - $total, 0);
        return [
            'base_price' => $basePrice,
            'discounts' => $discounts,
            'total_discount' => $total,
            'final_price' => $final,
        ];
    }

    private function buildDiscount(DiscountRule $rule, int $basePrice): array
    {
        $amount = $rule->discount_type === 'percent'
            ? (int) floor($basePrice * ($rule->discount_value / 100))
            : (int) $rule->discount_value;
        return [
            'id' => $rule->id,
            'name' => $rule->name,
            'amount' => $amount,
        ];
    }
}
