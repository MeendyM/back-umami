<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Categorías

    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory']);

    // Instituciones
    Route::get('/institutions', [AdminController::class, 'indexInstitution']);
    Route::post('/institutions', [AdminController::class, 'storeInstitution']);
    Route::put('/institutions/{institution}', [AdminController::class, 'updateInstitution']);
    Route::delete('/institutions/{institution}', [AdminController::class, 'destroyInstitution']);

    // Proveedores

    Route::post('/suppliers', [AdminController::class, 'storeSupplier']);
    Route::put('/suppliers/{supplier}', [AdminController::class, 'updateSupplier']);
    Route::delete('/suppliers/{supplier}', [AdminController::class, 'destroySupplier']);

    //Ordenes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/orderItems/{order}', [OrderController::class, 'products']);

    //Items
    Route::Post('/orderItems', [OrderItemController::class, 'addProduct']);

    // Carrito
    Route::get('/cart', [CartApiController::class, 'getCart']);
    Route::post('/cart/add', [CartApiController::class, 'addItem']);
});
