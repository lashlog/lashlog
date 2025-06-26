<?php

// app/Models/Staff.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    protected $fillable = ['owner_id', 'shop_id', 'name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];
}
