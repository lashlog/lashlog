<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReservationController;

use App\Http\Controllers\Api\ShopController;

Route::apiResource('customers', CustomerController::class);
Route::apiResource('menus', MenuController::class);
Route::apiResource('reservations', ReservationController::class);
Route::apiResource('shops', ShopController::class);
