<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Shop\AuthController;
use App\Http\Controllers\Api\Shop\CustomerController;
use App\Http\Controllers\Api\Shop\MenuController;
use App\Http\Controllers\Api\Shop\ReservationController;
use App\Http\Controllers\Api\Shop\ShopController;
use Illuminate\Support\Facades\Log;




// 管理者用API（スタッフログインが必要）
Route::prefix('shop')->group(function () {

    // ログインだけは未ログインでも通す
    Route::post('/login', [AuthController::class, 'login']);

    // 認証済みスタッフ向け
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('shops', ShopController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', function () {
            \Log::info('auth check: ' . json_encode(auth('shop')->check()));
            \Log::info('user: ' . json_encode(auth('shop')->user()));
            return auth('shop')->user();
        });
    });
});
