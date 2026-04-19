<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

//Route::get('/', [ProductoController::class, 'index']);

Route::get('/', function () {
    return "HELLO RAILWAY";
});