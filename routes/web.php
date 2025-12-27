<?php

use Illuminate\Support\Facades\Route;

// for vuejs routing
Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');
