<?php

// app/Models/Staff.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'staffs';
    protected $fillable = ['owner_id', 'shop_id', 'name', 'email', 'password', 'role', 'employment_type'];

    protected $hidden = ['password', 'remember_token'];
}
