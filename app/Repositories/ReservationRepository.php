<?php
// app/Repositories/ReservationRepository.php
namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository
{
    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function existsConflict(array $data): bool
    {
        return Reservation::where('staff_id', $data['staff_id'])
            ->where('reserved_date', $data['reserved_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();
    }
}
