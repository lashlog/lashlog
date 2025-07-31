<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\ShopSchedule;
use App\Models\ShopOpenHour;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;


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
    public function getBusinessHours(Request $request)
    {
        // dd($date);
        $dateStr = $request->query('date');
        Log::info('getBusinessHours called', ['date' => $dateStr]);
        $shopId = auth('shop')->id();
        $schedules = ShopSchedule::where('shop_id', $shopId)
            ->where('date', $dateStr)
            ->get();
        if ($schedules->isNotEmpty()) {
            return response()->json($schedules->map(function ($s) use ($dateStr) {
                return [
                    'is_closed' => $s->is_closed,
                    'open_time' => $s->open_time,
                    'close_time' => $s->close_time,
                    'date' => $dateStr,
                ];
            }));
        }

        $dayOfWeek = Carbon::parse($dateStr)->dayOfWeek; // 0〜6
        $default = ShopOpenHour::where('shop_id', $shopId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        return response()->json([[
            'is_closed' => $default?->is_closed ?? true,
            'open_time' => $default?->open_time,
            'close_time' => $default?->close_time,
            'date' => $dateStr,
        ]]);
    }
}
