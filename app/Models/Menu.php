<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'duration', 'price'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}

