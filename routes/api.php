<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::post('/transactions/checkout', [TransactionController::class, 'checkout']);
    Route::get('/transactions', [TransactionController::class, 'index']); // Riwayat transaksi
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
});