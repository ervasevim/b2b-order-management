<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name("login");
Route::get('/products', [ProductController::class, 'index']);

Route::middleware(['auth:api'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    Route::get('/orders', [OrderController::class, 'index'])->middleware('role:customer');
    Route::post('/orders', [OrderController::class, 'store'])->middleware('role:customer');
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});
