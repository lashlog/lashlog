<?php
// app/Models/Shop.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class Shop extends Authenticatable
{
    use HasApiTokens;

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
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
