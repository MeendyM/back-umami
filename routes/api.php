<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;

use App\Http\Controllers\Api\ProductController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //Ordenes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/orderItems/{order}', [OrderController::class, 'products']);
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
    Route::get('/user/orders/{id_order}', [OrderController::class, 'userOrderById']);

    //Items
    Route::Post('/orderItems', [OrderItemController::class, 'addProduct']);

    // Carrito
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::post('/cart/remove', [CartController::class, 'removeItem']);
    Route::get('/cart/getByUser', [CartController::class, 'getByUser']);
    Route::post('/cart/edit', [CartController::class, 'edit']);
    Route::post('/cart/editCustomTexts', [CartController::class, 'editCustomTexts']);
    Route::post('/cart/createOrder', [CartController::class, 'createOrder']);


});
    Route::get('/products', [ProductController::class, 'index']);
