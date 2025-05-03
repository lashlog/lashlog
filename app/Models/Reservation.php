<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['customer_id', 'menu_id', 'reserved_at', 'memo'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}

