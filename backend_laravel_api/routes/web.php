<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return response()->json([
        'status' => 'API is running',
    ]);
});

// Default login route (redirects to admin login)
Route::get('/login', function () {
    return redirect()->route('admin.login.form');
})->name('login');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login.form');
        Route::post('/', [AdminAuthController::class, 'login'])->name('login');
    });

    // Authenticated admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // User management routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('{id}', [AdminController::class, 'showUser'])->name('show');
            Route::get('{id}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('{id}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('{id}', [AdminController::class, 'deleteUser'])->name('delete');
        });
    });
});
