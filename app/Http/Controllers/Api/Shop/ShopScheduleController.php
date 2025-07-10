<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class ShopScheduleController extends Controller
{
    public function index(Request $request)
    {
        $shop = auth('shop')->user();

        // 今月 & 来月分を取得（必要に応じて調整）
        $schedules = $shop->schedules()
            ->whereDate('date', '>=', now()->startOfMonth())
            ->get()
            ->groupBy('date');

        return response()->json(
            $schedules->map(function ($slots) {
                // 休みのフラグが1つでもあれば、その日を「休み」として返す
                if ($slots->first()->is_closed) {
                    return ['closed' => true];
                }

                return [
                    'schedule' => $slots->map(function ($slot) {
                        return [
                            'start' => $slot->open_time,
                            'end' => $slot->close_time,
                        ];
                    }),
                ];
            })
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'schedule' => 'nullable|array',
            'schedule.*.start' => 'required_with:schedule.*.end',
            'schedule.*.end' => 'required_with:schedule.*.start',
            'closed' => 'nullable|boolean',
        ]);

        $shop = auth('shop')->user(); // または $request->user()->shop など
        $date = $request->date;

        // 既存のスケジュールを削除（上書きのため）
        $shop->schedules()->where('date', $date)->delete();
        // 休みとして登録
        if ($request->boolean('closed')) {
            $shop->schedules()->create([
                'date' => $date,
                'is_closed' => true,
                'open_time' => null,
                'close_time' => null,
            ]);
        } elseif (is_array($request->schedule))
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
