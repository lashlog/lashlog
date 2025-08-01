<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['staff_id', 'started_at', 'ended_at'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
