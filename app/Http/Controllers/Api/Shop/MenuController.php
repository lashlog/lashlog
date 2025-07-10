<?php
// app/Http/Controllers/Api/Shop/MenuController.php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $shopId = auth('shop')->id();
        $menus = Menu::with('category')
            ->where('shop_id', $shopId)
            ->orderBy('sort_order')
            ->get();

        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $shopId = auth('shop')->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'unit' => 'nullable|string|max:50', // 例：120本
            'is_active' => 'boolean',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        $menu = Menu::create([
            ...$validated,
            'shop_id' => $shopId,
        ]);

        return response()->json(['message' => 'メニューを登録しました', 'menu' => $menu]);
    }
    public function show($id)
    {
        $shopId = auth('shop')->id();
        $menu = Menu::where('shop_id', $shopId)->findOrFail($id);
        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $shopId = auth('shop')->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $menu = Menu::where('shop_id', $shopId)->findOrFail($id);
        $menu->update($validated);

        return response()->json(['message' => 'メニューを更新しました', 'menu' => $menu]);
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->shop_id !== auth('shop')->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $menu->delete();

        return response()->json(['message' => '削除しました']);
    }
}
