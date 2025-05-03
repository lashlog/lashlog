<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::with(['customer', 'menu'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'menu_id' => 'required|exists:menus,id',
            'reserved_at' => 'required|date',
            'memo' => 'nullable',
        ]);

        $reservation = Reservation::create($data);

        return response()->json($reservation->load(['customer', 'menu']), 201);
    }
}
