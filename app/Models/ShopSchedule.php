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
    public function isOpen()
    {
        // ここでは、スケジュールが開いているかどうかを判定するロジックを実装します
        return !$this->is_closed;
    }
    public function getDayOfWeekAttribute()
    {
        // 日付から曜日を取得するアクセサ
        return $this->date->format('w'); // 0 (日曜日) から 6 (土曜日)
    }
}
