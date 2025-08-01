<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $shopId = auth('shop')->id();
        $query = Sale::where('shop_id', $shopId);

        if ($request->has(['from', 'to'])) {
            $query->whereBetween('sale_date', [$request->from, $request->to]);
        }

        return $query->orderByDesc('sale_date')->get();
    }

    public function store(Request $request)
    {
        $shopId = auth('shop')->id();
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'amount' => 'required|integer',
            'memo' => 'nullable|string',
        ]);

        $sale = Sale::create([
            ...$validated,
            'shop_id' => $shopId,
            'staff_id' => auth('shop')->user()->id,
        ]);

        return response()->json($sale, 201);
    }
}
