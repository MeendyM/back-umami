<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ContactApiController;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReceipApiController;
use App\Http\Controllers\Api\SetApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\AuthGoogleApiController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/institutions', [AuthController::class, 'getInstitutions']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/google/callback', [AuthGoogleApiController::class, 'callback']);
Route::get('/auth/google/check', [AuthGoogleApiController::class, 'check']);
Route::get('/auth/google/login', [AuthGoogleApiController::class, 'login']);
Route::post('/user/forgot-password', [AuthController::class, 'forgotPassword']);

// Formulario de contacto (público)
Route::post('/contact', [ContactApiController::class, 'send']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/info', [AuthController::class, 'getUserInfo']);
    Route::post('/user/update-institution', [AuthController::class, 'updateInstitution']);


    Route::post('/user/resend-email-verification', [AuthController::class, 'resendEmailVerification']);

    // Primeros pasos
    Route::post('/user/first-steps', [AuthController::class, 'completeFirstSteps']);
    // Actualizar datos generales
    Route::post('/user/update-info', [AuthController::class, 'updateUserInfo']);


    // Carrito
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::post('/cart/remove', [CartController::class, 'removeItem']);
    Route::post('/cart/removeSet', [CartController::class, 'removeSet']);
    Route::get('/cart/getByUser', [CartController::class, 'getByUser']);
    Route::post('/cart/edit', [CartController::class, 'edit']);
    Route::post('/cart/editCustomTexts', [CartController::class, 'editCustomTexts']);
    Route::post('/cart/createOrder', [CartController::class, 'createOrder']);
    Route::post('/cart/addMultiple', [CartController::class, 'addMultipleItems']);

    //Ordenes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/orderItems/{order}', [OrderController::class, 'products']);
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
    Route::get('/user/orders/{id_order}', [OrderController::class, 'userOrderById']);

    // Pagos

    Route::post('/receips/add', [ReceipApiController::class, 'addReceip']);
    Route::get('/receips/order/{id_order}', [ReceipApiController::class, 'getReceipsByOrderId']);

    // Notificaciones
    Route::get('/notifications', [NotificationApiController::class, 'getUserNotifications']);
    Route::get('/notifications/unread', [NotificationApiController::class, 'getUnreadNotifications']);
    Route::get('/notifications/unread-count', [NotificationApiController::class, 'getUnreadCount']);
    Route::get('/notifications/{id}', [NotificationApiController::class, 'getNotification']);
    Route::post('/notifications/{id}/read', [NotificationApiController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationApiController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationApiController::class, 'deleteUserNotification']);
});
Route::get('/products', [ProductController::class, 'index']);
Route::get('/sets', [SetApiController::class, 'getSets']);
Route::get('/sets/products/{id_set}', [SetApiController::class, 'getProductsFromSets']);

