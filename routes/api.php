<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\MenuController;
use App\Http\Controllers\Api\Admin\ReservationController;
use App\Http\Controllers\Api\Admin\ShopController;


// 管理者用API（スタッフログインが必要）
Route::prefix('admin')->group(function () {

    // ログインだけは未ログインでも通す
    Route::post('/login', [AuthController::class, 'login']);

    // 認証済みスタッフ向け
    Route::middleware('auth:staff')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('shops', ShopController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', fn() => auth('staff')->user());
    });
});
