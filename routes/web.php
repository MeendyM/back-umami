<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\WebControllers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});
