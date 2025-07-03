<?php

// app/Models/ShopOpenHour.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOpenHour extends Model
{
    protected $fillable = ['shop_id', 'day_of_week', 'open_time', 'close_time', 'is_closed'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
