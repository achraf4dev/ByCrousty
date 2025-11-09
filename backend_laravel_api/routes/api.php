<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\LogController;
use App\Http\Controllers\Api\v1\PointsController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\ProductController;

Route::get('/v1/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 auth routes
Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot', [AuthController::class, 'forgot']);
    Route::post('reset', [AuthController::class, 'reset']);
    Route::get('verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'getUserProfile']);
        
        // QR code endpoints (requires authentication)
        Route::get('users/my-qr-code', [AuthController::class, 'getMyQrCode']);
        
        // Category and Product endpoints (public for authenticated users)
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/active', [CategoryController::class, 'active']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/active', [ProductController::class, 'active']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::get('categories/{id}/products', [ProductController::class, 'byCategory']);
        
        // Points endpoints
        Route::get('points', [PointsController::class, 'getUserPoints']); // Get own points
        Route::get('users/{id}/points', [PointsController::class, 'getUserPointsHistory']); // Get user points history
        
        // Admin only endpoints
        Route::middleware('admin')->group(function () {
            // Points management
            Route::post('points/award-by-qr', [PointsController::class, 'awardPointsByQrCode']); // Award points by QR scan
            Route::post('users/{id}/points', [PointsController::class, 'awardPointsToUser']); // Award points to specific user
            Route::get('admin/users-points', [PointsController::class, 'getAllUsersPoints']); // Get all users points
            
            // Category management
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{id}', [CategoryController::class, 'update']);
            Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
            
            // Product management
            Route::post('products', [ProductController::class, 'store']);
            Route::put('products/{id}', [ProductController::class, 'update']);
            Route::delete('products/{id}', [ProductController::class, 'destroy']);
        });
        
        // Login logs
        Route::get('users/{id}/login-logs', [LogController::class, 'index']);
    });
});

// Redirect all non-defined api routes to a 404 response
Route::fallback(function () {
    return response()->json(['message' => 'Resource not found'], 404);
});