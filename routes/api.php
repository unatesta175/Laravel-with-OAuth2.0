<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceCategoryTagController;


// CORS Test route
Route::get('/test-cors', function () {
    return response()->json([
        'success' => true,
        'message' => 'CORS is working!',
        'timestamp' => now()
    ]);
});

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);
Route::post('/auth/forgot-password', [AuthController::class, 'sendPasswordResetEmail']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Public data routes (for browsing services without authentication)
Route::get('/service-categories', [ServiceCategoryController::class, 'index']);
Route::get('/service-categories/{category}', [ServiceCategoryController::class, 'show']);
Route::get('/service-categories/{category}/services', [ServiceController::class, 'getByCategory']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/services/{service}/therapists', [ServiceController::class, 'getServiceTherapists']);
Route::get('/therapists/{therapist}/availability', [ServiceController::class, 'getTherapistAvailability']);

// Protected routes
Route::middleware('auth.cookie')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::match(['PUT'], '/auth/change-password', [AuthController::class, 'changePassword']);

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
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::get('/bookings/{booking}/receipt', [BookingController::class, 'generateReceipt']);
    Route::post('/bookings/{booking}/retry-payment', [BookingController::class, 'retryPayment']);

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

    // Bookings management (Admin and Therapist)
    Route::get('/admin/bookings', [BookingController::class, 'getAllBookings']);
    Route::put('/admin/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    // User list for calendar (Admin and Therapist can view therapists list)
    Route::get('/admin/users', [UserController::class, 'index']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // User management (create, update, delete only)
        Route::post('/admin/users', [UserController::class, 'store']);
        Route::get('/admin/users/{user}', [UserController::class, 'show']);
        Route::put('/admin/users/{user}', [UserController::class, 'update']);
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);

        // Service management
        Route::get('/admin/services', [ServiceController::class, 'adminIndex']);
        Route::post('/admin/services', [ServiceController::class, 'store']);
        Route::put('/admin/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy']);

        // Service Category management
        Route::get('/admin/service-categories', [ServiceCategoryController::class, 'adminIndex']);
        Route::post('/admin/service-categories', [ServiceCategoryController::class, 'store']);
        Route::get('/admin/service-categories/{category}', [ServiceCategoryController::class, 'adminShow']);
        Route::put('/admin/service-categories/{category}', [ServiceCategoryController::class, 'update']);
        Route::delete('/admin/service-categories/{category}', [ServiceCategoryController::class, 'destroy']);

        // Service Category Tags management
        Route::get('/admin/service-category-tags', [ServiceCategoryTagController::class, 'index']);
        Route::post('/admin/service-category-tags', [ServiceCategoryTagController::class, 'store']);
        Route::put('/admin/service-category-tags/{tag}', [ServiceCategoryTagController::class, 'update']);
        Route::delete('/admin/service-category-tags/{tag}', [ServiceCategoryTagController::class, 'destroy']);

        // Other admin-only routes
        Route::get('/admin/payments', [PaymentController::class, 'getAllPayments']);
        Route::get('/admin/reviews', [ReviewController::class, 'getAllReviews']);
    });
});

// ToyyibPay callback routes (public)
Route::post('/toyyibpay/callback', [BookingController::class, 'toyyibpayCallback']);
Route::get('/toyyibpay/callback', [BookingController::class, 'toyyibpayCallback']);

