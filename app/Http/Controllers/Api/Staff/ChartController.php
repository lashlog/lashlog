<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    public function store(Request $request)
    {
        // Placeholder for chart saving logic
        return response()->json(['message' => 'saved']);
    }
}
