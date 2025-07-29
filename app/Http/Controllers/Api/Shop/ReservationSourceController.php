<?php

namespace App\Http\Controllers\Api\Shop;

use App\Models\ReservationSource;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationSourceController extends Controller
{
    public function index()
    {
        $shopId = auth('shop')->user()->id; // 適宜修正
        $sources = ReservationSource::where('shop_id', $shopId)->orderBy('id')->get();

        return response()->json($sources);
    }
    public function store(Request $request)
    {
        $shopId = auth('shop')->user()->id; // 適宜修正

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $source = ReservationSource::create([
            'shop_id' => $shopId,
            'name' => $validated['name'],
            'is_active' => $validated['is_active'],
            'system_reserved' => false, // 一般登録時は false 固定
        ]);

        return response()->json($source, 201);
    }
    public function show($id)
    {
        $source = ReservationSource::findOrFail($id);
        return response()->json($source);
    }

    public function update(Request $request, $id)
    {
        $shopId = auth('shop')->user()->id; // 適宜修正

        $source = ReservationSource::where('shop_id', $shopId)
            ->where('id', $id)
            ->where('system_reserved', false) // 編集不可のものは除外
            ->firstOrFail();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $source->update($validated);
        return response()->json($source);
    }

    public function destroy($id)
    {
        $source = ReservationSource::findOrFail($id);
        $source->delete();
        return response()->json(null, 204);
    }
}
