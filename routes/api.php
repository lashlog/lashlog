<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Shop\AuthController;
use App\Http\Controllers\Api\Shop\CustomerController;
use App\Http\Controllers\Api\Shop\MenuController;
use App\Http\Controllers\Api\Shop\ReservationController;
use App\Http\Controllers\Api\Shop\ShopController;
use App\Http\Controllers\Api\Shop\ShopScheduleController;
use Illuminate\Support\Facades\Log;


Route::get('/sanctum/csrf-cookie', function () {
    // CSRFトークンを取得するためのエンドポイント
    return response('')->withCookie(
        cookie(
            'XSRF-TOKEN',
            csrf_token(),
            0, // 有効期限は0で、ブラウザセッションの間のみ有効
            null, // パスはデフォルトのルート
            null, // ドメインはデフォルトのルート
            false, // セキュアフラグはfalse（HTTPでも使用可能）
            false //
        )
    );
});

// 管理者用API（スタッフログインが必要）
Route::prefix('shop')->group(function () {

    // ログインだけは未ログインでも通す
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 認証済みスタッフ向け
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('menus', MenuController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('shops', ShopController::class);
        Route::get('/me', function () {
            return auth('shop')->user();
        });
        Route::post('/shop-schedules', [ShopScheduleController::class, 'store']);
    });
});
