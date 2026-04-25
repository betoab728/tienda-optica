<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/', [ProductoController::class, 'index']);
Route::get('/producto/{id}', [ProductoController::class, 'show'])->name('producto.detalle');

