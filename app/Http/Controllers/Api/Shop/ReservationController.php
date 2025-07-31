<?php

namespace App\Http\Controllers\Api\Shop;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReservationWithCustomerRequest;
use App\UseCases\Reservation\StoreReservationUseCase;
use App\UseCases\Reservation\StoreReservationWithCustomerUseCase;

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

    public function store(Request $request, StoreReservationUseCase $useCase)
    {
        $reservation = $useCase->execute($request);
        return response()->json($reservation);
    }

    public function storeWithCustomer(StoreReservationWithCustomerRequest $request,  StoreReservationWithCustomerUseCase $storeReservationWithCustomerUseCase)
    {
        $shopId = auth('shop')->user()->id;

        $data = $request->validated();
        $reservation = $storeReservationWithCustomerUseCase->execute($data, $shopId);

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
