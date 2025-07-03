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
        return Shop::with(['schedules', 'openHours'])->get();
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
        // 文字列 "0,6" → 配列 [0, 6]
        $closed_days = $request->filled('closed_days')
            ? array_map('intval', explode(',', $request->closed_days))
            : [];

        $shop->openHours()->delete(); // 一旦全削除

        // 営業時間保存
        foreach (range(0, 6) as $dow) {
            // 曜日ごとに営業時間を保存
            // 0: 日曜日, 1: 月曜日, ..., 6: 土曜日
            // $dow は 0 から 6 の整数
            // もし閉店日ならば open_time と close_time は null
            $shop->openHours()->create([
                'day_of_week' => $dow,
                'open_time' => in_array($dow, $closed_days) ? null : $request->weekday_open_time,
                'close_time' => in_array($dow, $closed_days) ? null : $request->weekday_close_time,
                'is_closed' => in_array($dow, $closed_days),
            ]);
        }

        // カレンダー別スケジュール保存
        $shop->schedules()->delete();
        $calendar = json_decode($request->calendar_schedule, true);
        foreach ($calendar as $date => $slots) {
            foreach ($slots as $slot) {
                $shop->schedules()->create([
                    'date' => $date,
                    'open_time' => $slot['start'],
                    'close_time' => $slot['end'],
                    'is_closed' => false,
                ]);
            }
        }
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
