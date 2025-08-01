<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'menu_id',
        'staff_id',
        'shop_id',
        'staff_id',
        'reserved_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'price',
        'memo',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    // App\Models\Reservation
    public function reservationSource()
    {
        return $this->belongsTo(ReservationSource::class);
    }
}
