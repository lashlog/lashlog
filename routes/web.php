<?php

use Illuminate\Support\Facades\Route;


Route::get('/{any}', function () {
    return view('app'); // resources/views/app.blade.php を返すように
})->where('any', '.*');
