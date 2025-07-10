<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $table = 'menu_categories';

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // アクティブなカテゴリだけ取得するスコープ（必要なら）
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // メニューとのリレーション（必要なら）
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
