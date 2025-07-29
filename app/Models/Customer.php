<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'shop_id',
        'phone',
        'email',
        'allergy_notes',
        'surgery_notes',
        'user_id', // 追加されたuser_id
    ];

    /**
     * 紐づくユーザー（Web予約ユーザー）
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 紐づく店舗
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
