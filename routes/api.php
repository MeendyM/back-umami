<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

     // Categorías
     Route::get('/categories', [AdminController::class, 'indexCategory']);
     Route::post('/categories', [AdminController::class, 'storeCategory']);
     Route::put('/categories/{category}', [AdminController::class, 'updateCategory']);
     Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory']);

     // Instituciones
     Route::get('/institutions', [AdminController::class, 'indexInstitution']);
     Route::post('/institutions', [AdminController::class, 'storeInstitution']);
     Route::put('/institutions/{institution}', [AdminController::class, 'updateInstitution']);
     Route::delete('/institutions/{institution}', [AdminController::class, 'destroyInstitution']);

     // Proveedores
     Route::get('/suppliers', [AdminController::class, 'indexSupplier']);
     Route::post('/suppliers', [AdminController::class, 'storeSupplier']);
     Route::put('/suppliers/{supplier}', [AdminController::class, 'updateSupplier']);
     Route::delete('/suppliers/{supplier}', [AdminController::class, 'destroySupplier']);
});
