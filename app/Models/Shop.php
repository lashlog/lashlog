<?php
// app/Models/Shop.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Shop extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable;

    protected $table = 'shops';
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'map_url',
        'weekday_open_time',
        'weekday_close_time',
        'weekend_open_time',
        'weekend_close_time',
        'closed_days'
    ];
    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed', // JSONカラムとして扱う
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    // app/Models/Shop.php

    public function openHours()
    {
        return $this->hasMany(ShopOpenHour::class);
    }

    public function schedules()
    {
        return $this->hasMany(ShopSchedule::class);
    }
}
