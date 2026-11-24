<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

// --- ROUTE PUBLIK ---
Route::post('/register', [AuthController::class, 'register']); // <-- PASTIKAN INI ADA
Route::post('/login', [AuthController::class, 'login']);

// --- ROUTE PRIVAT (Butuh Token) ---
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    
    Route::post('/transactions/checkout', [TransactionController::class, 'checkout']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
});