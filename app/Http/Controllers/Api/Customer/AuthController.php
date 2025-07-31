<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'line_id' => 'nullable|string|unique:users,line_id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'birthday' => $validated['birthday'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'line_id' => $validated['line_id'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['ログインに失敗しました'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json(['user' => Auth::user()]);
    }

    public function lineLogin(Request $request)
    {
        $lineId = $request->validate([
            'line_id' => 'required|string',
        ])['line_id'];

        $user = User::where('line_id', $lineId)->first();

        if (!$user) {
            return response()->json(['message' => 'not_registered'], 404);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['user' => $user]);
    }
}
