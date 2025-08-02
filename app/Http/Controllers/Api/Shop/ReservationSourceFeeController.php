<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\ReservationSource;
use App\Models\ReservationSourceFee;
use Illuminate\Http\Request;

class ReservationSourceFeeController extends Controller
{
    public function index($id)
    {
        $shopId = auth('shop')->user()->id;
        $source = ReservationSource::where('shop_id', $shopId)->findOrFail($id);

        return response()->json($source->fees()->orderBy('min_amount')->get());
    }

    public function store(Request $request, $id)
    {
        $shopId = auth('shop')->user()->id;
        $source = ReservationSource::where('shop_id', $shopId)->findOrFail($id);

        $data = $request->validate([
            'fees' => 'required|array|min:1',
            'fees.*.min_amount' => 'nullable|integer|min:0',
            'fees.*.max_amount' => 'nullable|integer|min:0',
            'fees.*.fee_type' => 'required|in:fixed,percent',
            'fees.*.fee_value' => 'required|integer|min:0',
        ]);

        $fees = $data['fees'];

        foreach ($fees as $fee) {
            $min = $fee['min_amount'] ?? 0;
            $max = $fee['max_amount'];
            if (!is_null($max) && $min >= $max) {
                return response()->json(['errors' => ['fees' => ['min_amount must be less than max_amount']]], 422);
            }
        }

        usort($fees, function ($a, $b) {
            return ($a['min_amount'] ?? 0) <=> ($b['min_amount'] ?? 0);
        });

        $prevMax = null;
        foreach ($fees as $fee) {
            $min = $fee['min_amount'] ?? 0;
            $max = $fee['max_amount'] ?? PHP_INT_MAX;
            if (!is_null($prevMax) && $min < $prevMax) {
                return response()->json(['errors' => ['fees' => ['fee ranges overlap']]], 422);
            }
            $prevMax = $max;
        }

        $source->fees()->delete();
        foreach ($fees as $fee) {
            $source->fees()->create($fee);
        }

        return response()->json($source->fees()->orderBy('min_amount')->get());
    }

    public function destroy(Request $request, $id)
    {
        $shopId = auth('shop')->user()->id;
        $feeId = $request->input('fee_id');

        if (!$feeId) {
            return response()->json(['errors' => ['fee_id' => ['fee_id is required']]], 422);
        }

        $fee = ReservationSourceFee::where('id', $feeId)
            ->whereHas('reservationSource', function ($q) use ($shopId, $id) {
                $q->where('shop_id', $shopId)->where('id', $id);
            })
            ->firstOrFail();

        $fee->delete();

        return response()->json(null, 204);
    }
}
