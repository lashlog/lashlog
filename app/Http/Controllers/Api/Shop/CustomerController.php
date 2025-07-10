<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $shopId = auth('shop')->id();

        $customers = Customer::where('shop_id', $shopId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $shopId = auth('shop')->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'allergy_notes' => 'nullable|string',
            'surgery_notes' => 'nullable|string',
        ]);

        $customer = new Customer($validated);
        $customer->shop_id = $shopId;
        $customer->save();

        return response()->json(['message' => '顧客を登録しました', 'customer' => $customer]);
    }
}
