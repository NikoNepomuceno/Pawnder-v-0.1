<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth Routes (Public)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Admin Dashboard Routes (Protected)
    Route::middleware(['web', 'auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Authentication
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Report Management Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/approved', [DashboardController::class, 'approvedReports'])
                ->name('approved')
                ->middleware('auth');
            Route::get('/archived', [DashboardController::class, 'archivedReports'])
                ->name('archived')
                ->middleware('auth');
            Route::get('/{report}', [DashboardController::class, 'showReport'])->name('show');
            Route::post('/{report}/approve', [DashboardController::class, 'approveReport'])->name('approve');
            Route::post('/{report}/reject', [DashboardController::class, 'rejectReport'])->name('reject');
        });

        // Test simple route
        Route::get('/test-route', function () {
            Log::info('Admin test route accessed');
            return 'Admin test route works!';
        })->name('test.route');
    });
});
