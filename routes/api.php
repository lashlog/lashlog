<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Shop\AuthController;
use App\Http\Controllers\Api\Shop\CustomerController;
use App\Http\Controllers\Api\Shop\MenuController;
use App\Http\Controllers\Api\Shop\ReservationController;
use App\Http\Controllers\Api\Shop\ShopController;
use App\Http\Controllers\Api\Shop\ShopScheduleController;
use App\Http\Controllers\Api\Shop\ShopOpenHourController;
use App\Http\Controllers\Api\Shop\StaffController;
use App\Http\Controllers\Api\Shop\MenuCategoryController;
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
        Route::get('/me', function () {
            return auth('shop')->user();
        });
        // 顧客のCRUD
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);


        Route::apiResource('menus', MenuController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('shops', ShopController::class);
        // スケジュールのCRUD
        Route::post('/shop-schedules', [ShopScheduleController::class, 'store']);
        Route::get('/shop-schedules', [ShopScheduleController::class, 'index']);
        // 曜日ごとの営業時間を取得・保存
        Route::get('/shop-open-hours', [ShopOpenHourController::class, 'index']);
        Route::post('/shop-open-hours', [ShopOpenHourController::class, 'store']);
        // スタッフのCRUD
        Route::get('staffs', [StaffController::class, 'index']);
        Route::post('staffs', [StaffController::class, 'store']);
        Route::get('staffs/{id}', [StaffController::class, 'show']);
        Route::patch('staffs/{id}', [StaffController::class, 'update']);
        Route::delete('staffs/{id}', [StaffController::class, 'destroy']);
        // メニューのCRUD
        Route::get('/menus', [MenuController::class, 'index']);
        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{id}', [MenuController::class, 'update']);
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
        // メニューカテゴリーのCRUD
        Route::get('/menu-categories', [MenuCategoryController::class, 'index']);
    });
});
