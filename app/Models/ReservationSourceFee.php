<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSourceFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_source_id',
        'min_amount',
        'max_amount',
        'fee_type',
        'fee_value',
    ];

    public function reservationSource()
    {
        return $this->belongsTo(ReservationSource::class);
    }

    /**
     * Calculate fee based on price and reservation source.
     */
    public static function calculate(int $reservationSourceId, int $price): int
    {
        $fee = self::where('reservation_source_id', $reservationSourceId)
            ->where(function ($q) use ($price) {
                $q->where(function ($query) use ($price) {
                    $query->whereNull('min_amount')->orWhere('min_amount', '<=', $price);
                })->where(function ($query) use ($price) {
                    $query->whereNull('max_amount')->orWhere('max_amount', '>', $price);
                });
            })
            ->orderBy('min_amount')
            ->first();

        if (!$fee) {
            return 0;
        }

        return $fee->fee_type === 'fixed'
            ? (int) $fee->fee_value
            : (int) floor($price * $fee->fee_value / 100);
    }
}
