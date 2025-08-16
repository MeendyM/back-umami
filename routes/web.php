<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebControllers;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Auth;

// Ruta raíz con redirección condicional
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.custom');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset.custom.post');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin.only',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/products',[WebControllers::class, 'products'])->name('products');
    Route::get('/collections',[WebControllers::class, 'collections'])->name('collections');
    
    Route::get('/discounts',[WebControllers::class, 'discounts'])->name('discounts');
    Route::get('/orders',[WebControllers::class, 'orders'])->name('orders');
    Route::get('/order-items',[WebControllers::class, 'orderItems'])->name('order-items');
    Route::get('/users',[UserManagementController::class, 'users'])->name('users');
    Route::get('/institutions',[UserManagementController::class, 'institutions'])->name('institutions');
    Route::get('/notifications',[WebControllers::class, 'notifications'])->name('notifications');
   
});
