<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'condition_type',
        'condition_value',
        'discount_type',
        'discount_value',
        'target_menu_ids',
        'is_active',
    ];

    protected $casts = [
        'target_menu_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
