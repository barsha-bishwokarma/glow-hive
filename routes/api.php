<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\KhaltiController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

 
// Public routes
Route::get('/category/index', [CategoryController::class, 'index']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);

Route::get('/brand/index', [BrandController::class, 'index']);
Route::get('/brand/{slug}', [BrandController::class, 'show']);

Route::get('/product/index', [ProductController::class, 'index']);
Route::get('/product/{slug}', [ProductController::class, 'show']); 

Route::get('/product/{id}/images', [ProductImageController::class, 'index']);

Route::get('/review/index/{id}', [ReviewController::class, 'index']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login_response'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::delete('/logout', [AuthController::class, 'logout']);
});

// Customer routes
Route::middleware(['auth:sanctum', 'customer'])->group(function () {
    Route::get('/cart/index', [CartController::class, 'index']);
    Route::post('/cart/store', [CartController::class, 'store']);
    Route::patch('/cart/update/{id}', [CartController::class, 'update']);
    Route::delete('/cart/delete/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    Route::get('/checkout', [CheckoutController::class, 'index']);

    // Orders - Customer
    Route::post('/order/store', [OrderController::class, 'store']);
    Route::get('/order/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/order/show/{id}', [OrderController::class, 'show']);
    Route::patch('/order/cancel/{id}', [OrderController::class, 'cancel']);

    Route::post('/review/store/{id}', [ReviewController::class, 'store']);
    Route::patch('/review/update/{id}', [ReviewController::class, 'update']);
    Route::delete('/review/delete/{id}', [ReviewController::class, 'destroy']);
});

Route::get('/khalti/callback', [KhaltiController::class, 'callback'])->name('khalti.callback');
