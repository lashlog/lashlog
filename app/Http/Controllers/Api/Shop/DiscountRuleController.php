<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\DiscountRule;
use Illuminate\Http\Request;

class DiscountRuleController extends Controller
{
    public function index()
    {
        return DiscountRule::where('shop_id', auth('shop')->id())->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'condition_type' => 'required|in:first_time,repeat_within_days,birthday_month,reservation_source,coupon',
            'condition_value' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer',
            'target_menu_ids' => 'nullable|array',
            'target_menu_ids.*' => 'integer|exists:menus,id',
            'is_active' => 'boolean',
        ]);
        $data['shop_id'] = auth('shop')->id();
        $rule = DiscountRule::create($data);
        return response()->json($rule, 201);
    }

    public function show($id)
    {
        $rule = DiscountRule::where('shop_id', auth('shop')->id())->findOrFail($id);
        return response()->json($rule);
    }

    public function update(Request $request, $id)
    {
        $rule = DiscountRule::where('shop_id', auth('shop')->id())->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'condition_type' => 'required|in:first_time,repeat_within_days,birthday_month,reservation_source,coupon',
            'condition_value' => 'nullable|string',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|integer',
            'target_menu_ids' => 'nullable|array',
            'target_menu_ids.*' => 'integer|exists:menus,id',
            'is_active' => 'boolean',
        ]);
        $rule->update($data);
        return response()->json($rule);
    }

    public function destroy($id)
    {
        $rule = DiscountRule::where('shop_id', auth('shop')->id())->findOrFail($id);
        $rule->delete();
        return response()->json(['message' => 'deleted']);
    }
}
