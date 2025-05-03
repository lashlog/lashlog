<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'allergy_notes', 'surgery_notes'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
