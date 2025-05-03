<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return Menu::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'duration' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $menu = Menu::create($data);

        return response()->json($menu, 201);
    }
}

