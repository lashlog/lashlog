<?php

namespace App\Http\Controllers\Api\Shop;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['customer', 'menu', 'staff']);

        if ($request->has('date')) {
            $query->whereDate('reserved_date', $request->date);
        } elseif ($request->has(['from', 'to'])) {
            $query->whereBetween('reserved_date', [$request->from, $request->to]);
        }
        $query->where('shop_id', auth('shop')->user()->id);
        return $query->get();
    }
    public function store(Request $request)
    {
        $request->merge([
            'shop_id' => auth('shop')->user()->id,
        ]);

        $validated = $request->validate([
            'reserved_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'staff_id' => 'required|exists:staffs,id',
            'customer_id' => 'nullable|exists:customers,id',
            'menu_id' => 'required|exists:menus,id',
            'memo' => 'nullable|string',
            'shop_id' => 'required|exists:shops,id',
        ]);
        // 予約の重複チェック
        $conflict = Reservation::where('staff_id', $validated['staff_id'])
            ->where('reserved_date', $validated['reserved_date'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<=', $validated['start_time'])
                        ->where('end_time', '>=', $validated['end_time']);
                });
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => ['この時間帯はすでに予約が入っています。'],
            ]);
        }
        $reservation = Reservation::create($validated);

        return response()->json($reservation);
    }

    public function show($id)
    {
        $reservation = Reservation::with(['customer', 'menu', 'staff'])->findOrFail($id);
        return response()->json(['reservation' => $reservation]);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $reservation->update($validated);

        return response()->json(['reservation' => $reservation->fresh(['customer', 'menu', 'staff'])]);
    }

    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => '予約が見つかりませんでした'], 404);
        }

        $reservation->delete();
        return response()->json(['message' => '予約を削除しました'], 200);
    }
}
