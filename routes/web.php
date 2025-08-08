<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebControllers;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/products',[WebControllers::class, 'products'])->name('products');
    Route::get('/collections',[WebControllers::class, 'collections'])->name('collections');
    
    Route::get('/discounts',[WebControllers::class, 'discounts'])->name('discounts');
    Route::get('/orders',[WebControllers::class, 'orders'])->name('orders');
    Route::get('/users',[UserManagementController::class, 'users'])->name('users');
    Route::get('/institutions',[UserManagementController::class, 'institutions'])->name('institutions');
    Route::get('/notifications',[NotificationController::class, 'index'])->name('notifications');
});
