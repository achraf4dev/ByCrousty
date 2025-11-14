<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

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
    // Handle root admin path based on authentication
    Route::get('/', function () {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return app(AdminAuthController::class)->showLogin();
    })->name('login.form');
    
    // Login route for guests only
    Route::middleware('guest')->group(function () {
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
            
            // Points management routes
            Route::post('{id}/award-points', [AdminController::class, 'awardPoints'])->name('award-points');
        });
        
        // Points management routes
        Route::get('points-history', [AdminController::class, 'pointsHistory'])->name('points-history');
        
        // Category management routes
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        
        // Product management routes
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
    });
});
