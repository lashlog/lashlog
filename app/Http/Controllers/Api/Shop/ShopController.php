<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        // 今は1店舗だけ表示（将来は複数対応可能）
        return Shop::all();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $shop = Shop::create($data);
        return response()->json($shop, 201);
    }

    public function show(Shop $shop)
    {
        return $shop;
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $this->validateData($request);
        $shop->update($data);
        return response()->json($shop);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json(['message' => '削除しました']);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'map_url' => 'nullable|string',
            'weekday_open_time' => 'nullable',
            'weekday_close_time' => 'nullable',
            'weekend_open_time' => 'nullable',
            'weekend_close_time' => 'nullable',
            'closed_days' => 'nullable|string',
        ]);
    }
}
