<?php

namespace App\Http\Controllers\Api\Admin;

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
        // バリデーション
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // staff guardで認証
        if (!Auth::guard('staff')->attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        $request->session()->regenerate(); // セッション固定攻撃対策

        return response()->json([
            'message' => 'ログイン成功',
            'staff' => Auth::guard('staff')->user(),
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'ログアウトしました']);
    }

    /**
     * ログイン中のスタッフ情報取得
     */
    public function me(Request $request)
    {
        return response()->json(Auth::guard('staff')->user());
    }
}
