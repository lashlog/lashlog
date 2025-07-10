<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        // アクティブなカテゴリのみ取得
        $categories = MenuCategory::where('is_active', true)->get();
        return response()->json($categories);
    }
}
