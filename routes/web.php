<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PrescriptionController;

Route::get('/', [ProductoController::class, 'index']);
Route::get('/producto/{id}', [ProductoController::class, 'show'])->name('producto.detalle');

// Cart Page (HTML)
Route::get('/carrito', [CartController::class, 'show'])->name('cart.show');

// Cart API Routes (JSON)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Prescription API
Route::prefix('prescription')->name('prescription.')->group(function () {
    Route::post('/analyze', [PrescriptionController::class, 'analyze'])->name('analyze');
});

Route::get('/test-gemini', function (\App\Services\GeminiService $gemini) {
     $respuesta = $gemini->analizarTexto(
        'Responde unicamente: API funcionando correctamente' );
         dd($respuesta);
    });
