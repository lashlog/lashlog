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
use App\Http\Controllers\Api\Shop\SaleController;
use App\Http\Controllers\Api\Shop\ReservationSourceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
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

// 顧客向けAPI
Route::get('/users', [UserController::class, 'index']);
Route::prefix('customer')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::post('/line-login', [CustomerAuthController::class, 'lineLogin']);
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
        Route::get('customers/{id}', [CustomerController::class, 'show']);
        Route::patch('customers/{id}', [CustomerController::class, 'update']);
        // 予約元媒体のCRUD
        Route::get('/reservation-sources', [ReservationSourceController::class, 'index']);
        Route::post('/reservation-sources', [ReservationSourceController::class, 'store']);
        Route::get('/reservation-sources/{id}', [ReservationSourceController::class, 'show']);
        Route::put('/reservation-sources/{id}', [ReservationSourceController::class, 'update']);
        Route::delete('/reservation-sources/{id}', [ReservationSourceController::class, 'destroy']);
        // 予約の取得・登録など（/api/shop/reservations）
        Route::get('reservations', [ReservationController::class, 'index']);
        Route::post('reservations', [ReservationController::class, 'store']);
        Route::get('reservations/{id}', [ReservationController::class, 'show']);
        Route::put('reservations/{id}', [ReservationController::class, 'update']);
        Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);
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

        // 売上の取得・登録
        Route::get('/sales', [SaleController::class, 'index']);
        Route::post('/sales', [SaleController::class, 'store']);

        Route::get('/business-hours', [ShopController::class, 'getBusinessHours']);
    });
});
