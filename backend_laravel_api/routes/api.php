<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\LogController;

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
        Route::get('users/my-qr-code', [AuthController::class, 'getMyQrCode']); // Convenient endpoint for own QR code
        // return login logs for the authenticated user
        Route::get('users/{id}/login-logs', [LogController::class, 'index']);
    });
});


// Redirect all non-defined api routes to a 404 response
Route::fallback(function () {
    return response()->json(['message' => 'Resource not found'], 404);
});