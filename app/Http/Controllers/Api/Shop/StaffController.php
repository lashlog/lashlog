<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;

use App\Models\Shop;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    public function index()
    {
        $shopId = auth('shop')->user()->shop_id;
        $staffs = Staff::where('shop_id', $shopId)->get();

        return response()->json($staffs);
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        // 自分以外のみ削除できるようにするなど条件は必要に応じて追加
        $staff->delete();

        return response()->json(['message' => '削除しました']);
    }
}
