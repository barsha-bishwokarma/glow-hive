<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // category
    Route::post('/category/store', [CategoryController::class, 'store']);
    Route::patch('/category/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy']);

    //brands
    Route::post('/brand/store', [BrandController::class, 'store']);
    Route::patch('/brand/update/{id}', [BrandController::class, 'update']);
    Route::delete('/brand/delete/{id}', [BrandController::class, 'destroy']);

    //products
    Route::post('/product/store', [ProductController::class, 'store']);
    Route::patch('/product/update/{id}', [ProductController::class, 'update']);
    Route::delete('/product/delete/{id}', [ProductController::class, 'destroy']);

    // Orders - Admin
    Route::get('/order/index', [OrderController::class, 'index']);
    Route::patch('/order/status/{id}', [OrderController::class, 'updateStatus']);

    Route::post('/product/{id}/images',          [ProductImageController::class, 'store']);
    Route::patch('/product/{id}/images/{imageId}/primary', [ProductImageController::class, 'setPrimary']);
    Route::delete('/product/images/{id}',        [ProductImageController::class, 'destroy']);
});

// category
Route::get('/category/index', [CategoryController::class, 'index']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);

//brands
Route::get('/brand/index', [BrandController::class, 'index']);
Route::get('/brand/{slug}', [BrandController::class, 'show']);

//products
Route::get('/product/index', [ProductController::class, 'index']);

//auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login_response'])->name("login");

Route::get('/review/index/{id}', [ReviewController::class, 'index']);

// Product Images - Public
Route::get('/product/{id}/images', [ProductImageController::class, 'index']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::delete('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'customer'])->group(function () {
    Route::get('/cart/index', [CartController::class, 'index']);
    Route::post('/cart/store', [CartController::class, 'store']);
    Route::patch('/cart/update/{id}', [CartController::class, 'update']);
    Route::delete('/cart/delete/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    Route::get('/checkout', [CheckoutController::class, 'index']);

    // Orders - Customer
    Route::post('/order/store',  [OrderController::class, 'store']);
    Route::get('/order/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/order/show/{id}',    [OrderController::class, 'show']);
    Route::patch('/order/cancel/{id}', [OrderController::class, 'cancel']);

    Route::post('/review/store/{id}',   [ReviewController::class, 'store']);
    Route::patch('/review/update/{id}', [ReviewController::class, 'update']);
    Route::delete('/review/delete/{id}', [ReviewController::class, 'destroy']);
});
