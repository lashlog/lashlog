<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id',
        'menu_id',
        'staff_id',
        'reserved_at',
        'start_time',
        'end_time',
        'duration_minutes',
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
}
