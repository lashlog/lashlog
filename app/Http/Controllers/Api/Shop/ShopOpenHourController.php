<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class ShopOpenHourController extends Controller
{
    public function index()
    {
        $shop = auth('shop')->user();
        $hours = $shop->openHours()->get(); // hasManyで定義
        return response()->json($hours);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hours' => 'required|array',
            'hours.*.day_of_week' => 'required|integer|between:0,6',
            'hours.*.open_time' => 'required|date_format:H:i',
            'hours.*.close_time' => 'required|date_format:H:i|after:hours.*.open_time',
        ]);

        $shop = auth('shop')->user();

        // 上書き保存の例
        $shop->openHours()->delete();
        foreach ($request->hours as $hour) {
            $shop->openHours()->create($hour);
        }

        return response()->json(['message' => '保存しました']);
    }
}
