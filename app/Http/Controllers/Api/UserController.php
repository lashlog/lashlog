<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        return response()->json(User::all());
    }
}
