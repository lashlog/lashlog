<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopScheduleController extends Controller
{
    public function index() {}

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'schedule' => 'required|array',
            'schedule.*.start' => 'required',
            'schedule.*.end' => 'required',
        ]);

        $shop = auth()->user()->shop; // または $request->user()->shop など
        $date = $request->date;

        // 既存のスケジュールを削除（上書きのため）
        $shop->schedules()->where('date', $date)->delete();

        foreach ($request->schedule as $slot) {
            $shop->schedules()->create([
                'date' => $date,
                'open_time' => $slot['start'],
                'close_time' => $slot['end'],
            ]);
        }

        return response()->json(['message' => '保存しました']);
    }

    public function show(Shop $shop) {}

    public function update(Request $request, Shop $shop) {}

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json(['message' => '削除しました']);
    }

    private function validateData(Request $request) {}
}
