<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'paid_leave'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
