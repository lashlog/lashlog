<?php
// app/Models/Shop.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // 必要に応じて他のカラムも追加

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
