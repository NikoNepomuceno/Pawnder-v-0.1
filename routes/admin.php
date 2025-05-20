<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth Routes (Public)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Admin Dashboard Routes (Protected)
    Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Report Management Routes
        Route::get('/reports/{report}', [DashboardController::class, 'showReport'])->name('reports.show');
        Route::post('/reports/{report}/approve', [DashboardController::class, 'approveReport'])->name('reports.approve');
        Route::post('/reports/{report}/reject', [DashboardController::class, 'rejectReport'])->name('reports.reject');
    });
});
