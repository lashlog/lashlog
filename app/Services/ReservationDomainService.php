<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ShopSchedule;
use App\Models\ShopOpenHour;
use Carbon\Carbon;
use App\Repositories\ReservationRepository;

class ReservationDomainService
{
    protected $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }


    public function hasConflict(array $data): bool
    {
        return $this->reservationRepository->existsConflict($data);
    }

    public function isWithinBusinessHours(array $data): bool
    {
        $date = new Carbon($data['reserved_date']);
        $shopId = $data['shop_id'];

        $schedule = ShopSchedule::where('shop_id', $shopId)
            ->where('date', $date->format('Y-m-d'))
            ->first();

        if ($schedule) {
            if ($schedule->is_closed) return false;
            return $data['start_time'] >= $schedule->open_time && $data['end_time'] <= $schedule->close_time;
        }

        $openHour = ShopOpenHour::where('shop_id', $shopId)
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (!$openHour || $openHour->is_closed) return false;

        return $data['start_time'] >= $openHour->open_time && $data['end_time'] <= $openHour->close_time;
    }
}
