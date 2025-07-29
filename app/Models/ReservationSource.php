<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// App\Models\ReservationSource
class ReservationSource extends Model
{
    use HasFactory;
    protected $fillable = ['shop_id', 'name', 'is_active', 'system_reserved'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
