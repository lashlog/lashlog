<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Shop\AuthController;
use App\Http\Controllers\Api\Shop\CustomerController;
use App\Http\Controllers\Api\Shop\MenuController;
use App\Http\Controllers\Api\Shop\ReservationController;
use App\Http\Controllers\Api\Shop\ShopController;


// 管理者用API（スタッフログインが必要）
Route::prefix('shop')->group(function () {

    // ログインだけは未ログインでも通す
    Route::post('/login', [AuthController::class, 'login']);

    // 認証済みスタッフ向け
    Route::middleware('auth:shop')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('shops', ShopController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', fn() => auth('shop')->user());
    });
});
