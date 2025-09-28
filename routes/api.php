<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\ServiceCategoryController;


// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);
Route::post('/auth/forgot-password', [AuthController::class, 'sendPasswordResetEmail']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Public data routes (for browsing services without authentication)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/service-categories', [ServiceCategoryController::class, 'index']);
Route::get('/service-categories/{category}', [ServiceCategoryController::class, 'show']);
Route::get('/categories/{category}/services', [ServiceController::class, 'getByCategory']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Protected routes
Route::middleware('auth.cookie')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);

    // Categories (Admin only)
    Route::middleware('admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // Services (Admin only for CUD operations)
    Route::middleware('admin')->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
        Route::post('/services/{service}/therapists', [ServiceController::class, 'assignTherapist']);
        Route::delete('/services/{service}/therapists/{therapist}', [ServiceController::class, 'removeTherapist']);
    });

    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::put('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    // Payments
    Route::post('/payments/checkout', [PaymentController::class, 'checkout']);
    Route::put('/payments/{payment}/complete', [PaymentController::class, 'complete']);
    Route::get('/payments/{payment}/status', [PaymentController::class, 'status']);

    // Payment webhooks (these might need to be outside auth in real implementation)
    Route::post('/payments/webhook/toyyibpay', [PaymentController::class, 'toyyibpayWebhook']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);
    Route::get('/therapists/{therapist}/reviews', [ReviewController::class, 'getTherapistReviews']);
    Route::put('/reviews/{review}/approve', [ReviewController::class, 'approve'])->middleware('admin');

    // Dashboard data
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Therapist availability
    Route::get('/therapists/{therapist}/availability', [ServiceController::class, 'getTherapistAvailability']);
    Route::get('/services/{service}/therapists', [ServiceController::class, 'getServiceTherapists']);

    // Admin routes
    Route::middleware('admin')->group(function () {
        // User management
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
        Route::post('/admin/users', [AuthController::class, 'createUser']);
        Route::get('/admin/users/{user}', [AuthController::class, 'getUser']);
        Route::put('/admin/users/{user}', [AuthController::class, 'updateUser']);
        Route::delete('/admin/users/{user}', [AuthController::class, 'deleteUser']);

        // Service management
        Route::get('/admin/services', [ServiceController::class, 'adminIndex']);
        Route::post('/admin/services', [ServiceController::class, 'store']);
        Route::put('/admin/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy']);

        // Category management
        Route::get('/admin/categories', [CategoryController::class, 'index']);
        Route::post('/admin/categories', [CategoryController::class, 'store']);
        Route::get('/admin/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/admin/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);

        // Other admin routes
        Route::get('/admin/bookings', [BookingController::class, 'getAllBookings']);
        Route::get('/admin/payments', [PaymentController::class, 'getAllPayments']);
        Route::get('/admin/reviews', [ReviewController::class, 'getAllReviews']);
    });
});

