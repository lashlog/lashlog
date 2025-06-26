<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Shop $shop)
    {
        return $shop->menus()->orderBy('sort_order')->get();
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
