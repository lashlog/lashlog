<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $shopId = auth('shop')->user()->id;
        $staffs = Staff::where('shop_id', $shopId)->get();
        return response()->json($staffs);
    }

    public function store(Request $request)
    {
        $shopId = auth('shop')->user()->id;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:staffs,email',
            'role'     => ['required', Rule::in(['owner', 'staff'])],
            'password' => 'required|string|min:6|confirmed',
        ]);

        $staff = new Staff();
        $staff->shop_id = $shopId;
        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->role = $validated['role'];
        $staff->password = Hash::make($validated['password']);
        $staff->save();

        return response()->json(['message' => 'スタッフを追加しました', 'staff' => $staff]);
    }

    public function show($id)
    {
        $shopId = auth('shop')->user()->id;
        $staff = Staff::findOrFail($id);

        if ($staff->shop_id !== $shopId) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        return response()->json($staff);
    }

    public function update(Request $request, $id)
    {
        $shopId = auth('shop')->user()->id;
        $staff = Staff::findOrFail($id);

        if ($staff->shop_id !== $shopId) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('staff')->ignore($staff->id)],
            'role'     => ['required', Rule::in(['owner', 'staff'])],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->role = $validated['role'];
        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return response()->json(['message' => 'スタッフ情報を更新しました', 'staff' => $staff]);
    }
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        $shopId = auth('shop')->user()->id;
        if ($staff->shop_id !== $shopId) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        // 自分以外のみ削除できるようにするなど条件は必要に応じて追加
        $staff->delete();

        return response()->json(['message' => '削除しました']);
    }
}
