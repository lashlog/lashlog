<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::with(['customer', 'menu', 'staff'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'menu_id' => 'required|exists:menus,id',
            'staff_id' => 'required|exists:staff,id',
            'reserved_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'memo' => 'nullable|string',
        ]);

        $reservation = Reservation::create($data);

        return response()->json(['reservation' => $reservation->load(['customer', 'menu', 'staff'])], 201);
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
