<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $rules = [
            'date' => 'required|date',
            'paid_leave' => 'boolean',
        ];
        if ($staff->employment_type === 'parttime') {
            $rules['start_time'] = 'required';
            $rules['end_time'] = 'required|after:start_time';
        } else {
            $rules['start_time'] = 'nullable';
            $rules['end_time'] = 'nullable';
        }
        $data = $request->validate($rules);
        $data['staff_id'] = $staff->id;
        $shift = Shift::create($data);
        return response()->json($shift);
    }

    public function index()
    {
        $shifts = Shift::where('staff_id', Auth::id())->get();
        return response()->json($shifts);
    }
}
