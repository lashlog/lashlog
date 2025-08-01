<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'message' => 'ログイン成功',
                'user' => Auth::guard('staff')->user(),
            ]);
        }
        return response()->json(['message' => 'ログイン失敗'], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }

    public function me(Request $request)
    {
        return Auth::guard('staff')->user();
    }
}
