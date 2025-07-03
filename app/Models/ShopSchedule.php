<?php

// app/Models/ShopSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSchedule extends Model
{
    protected $fillable = ['shop_id', 'date', 'open_time', 'close_time', 'is_closed'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
