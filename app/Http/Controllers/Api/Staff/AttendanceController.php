<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function startWork(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $attendance = Attendance::create([
            'staff_id' => $staff->id,
            'started_at' => now(),
        ]);
        return response()->json($attendance);
    }

    public function endWork(Request $request, $id)
    {
        $attendance = Attendance::where('staff_id', Auth::id())->findOrFail($id);
        $attendance->ended_at = now();
        $attendance->save();
        return response()->json($attendance);
    }

    public function index()
    {
        $records = Attendance::where('staff_id', Auth::id())->get();
        return response()->json($records);
    }
}
