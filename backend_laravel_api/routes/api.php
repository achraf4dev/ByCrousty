<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\LogController;
use App\Http\Controllers\Api\v1\PointsController;
use App\Http\Controllers\Api\v1\CategoryController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\CartController;
use App\Http\Controllers\Api\v1\StaticContentController;

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

    // ==================== CLIENT ROUTES ====================
    
    // Client Static Content (Public)
    Route::get('static/about', [StaticContentController::class, 'about']);
    Route::get('static/contact', [StaticContentController::class, 'contact']);
    Route::get('static/find-us', [StaticContentController::class, 'findUs']);

    // Public endpoints (no authentication required)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/active', [CategoryController::class, 'active']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/active', [ProductController::class, 'active']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('categories/{id}/products', [ProductController::class, 'byCategory']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'getUserProfile']);
        
        // ==================== CART ROUTES ====================
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart/add', [CartController::class, 'add']);
        Route::delete('cart/remove/{id}', [CartController::class, 'remove']);
        Route::post('cart/clear', [CartController::class, 'clear']);
        Route::post('cart/sync', [CartController::class, 'sync']);
        
        // QR code endpoints (requires authentication)
        Route::get('users/my-qr-code', [AuthController::class, 'getMyQrCode']);
        
        // Points endpoints
        Route::get('points', [PointsController::class, 'getUserPoints']); // Get own points
        Route::get('users/{id}/points', [PointsController::class, 'getUserPointsHistory']); // Get user points history
        
        // Admin only endpoints
        Route::middleware('admin')->group(function () {
            // Points management
            Route::post('points/award-by-qr', [PointsController::class, 'awardPointsByQrCode']); // Award points by QR scan
            Route::post('users/{id}/points', [PointsController::class, 'awardPointsToUser']); // Award points to specific user
            Route::get('admin/users-points', [PointsController::class, 'getAllUsersPoints']); // Get all users points
            
            // Admin new endpoints
            Route::get('admin/user-by-qr/{code}', [AdminController::class, 'getUserByQr']); // Get user by QR code
            Route::post('admin/add-points', [AdminController::class, 'addPoints']); // Add points to user
            Route::get('admin/orders', [AdminController::class, 'getOrders']); // Get all orders
            Route::post('admin/orders/{id}/status', [AdminController::class, 'updateOrderStatus']); // Update order status
            Route::get('admin/summary', [AdminController::class, 'getSummary']); // Get admin dashboard summary
            Route::get('admin/history/points', [AdminController::class, 'getPointsHistory']); // Points history with search
            Route::get('admin/history/orders', [AdminController::class, 'getOrdersHistory']); // Orders history with search
            
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