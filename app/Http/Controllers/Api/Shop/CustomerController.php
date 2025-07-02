<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'allergy_notes' => 'nullable',
            'surgery_notes' => 'nullable',
        ]);

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }
}
