<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('shop')->attempt($credentials)) {
            $request->session()->regenerate();
            $shop = Auth::guard('shop')->user();

            return response()->json([
                'message' => 'ログイン成功',
                'user' => $shop
            ]);
        }
        return response()->json([
            'message' => 'ログイン失敗',
        ], 401);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('shop')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * ログイン中のスタッフ情報取得
     */
    public function me(Request $request)
    {
        // dd("test"); // デバッグ用のダンプ
        return Auth::guard('shop')->user();
    }
}
