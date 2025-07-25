<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['name', 'shop_id', 'duration_minutes', 'price', 'menu_category_id', 'unit', 'is_active', 'description', 'sort_order'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }
}
