<?php

use App\Mail\TestMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PostReportController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\RegisterController;

// Include route files
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/posts.php';

// Protected Routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/view-profile', [UserController::class, 'viewProfile'])->name('view-profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Logout route
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/')->with('success', 'Logged out successfully!');
    })->name('logout');

    // Settings route with email verification middleware
    Route::middleware('verified')->group(function () {
        Route::get('/settings', function () {
            return view('Setting');
        })->name('settings');
    });

    // Trash routes
    Route::get('/trash', [PostController::class, 'trash'])->name('trash.index');
    Route::post('/trash/{id}/restore', [PostController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{id}/force-delete', [PostController::class, 'forceDelete'])->name('trash.forceDelete');

    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
});

// Add a route to get all photos for a post
Route::get('/posts/{post}/photos', function (App\Models\Post $post) {
    return response()->json([
        'success' => true,
        'photo_urls' => $post->photo_urls
    ]);
})->name('posts.photos');

// Post report routes
Route::post('/posts/{post}/report', [PostReportController::class, 'store'])->name('posts.report');

// Email Verification Routes
Route::get('/verify-email', [VerificationController::class, 'show'])
    ->name('verify.email.page');

Route::post('/verify-email', [VerificationController::class, 'verify'])
    ->name('verify.email');

Route::post('/resend-verification', [VerificationController::class, 'resend'])
    ->name('resend.verification');

// Test route for debugging
Route::get('/test-approved-reports', function () {
    \Log::info('Test route accessed');
    return 'Test route works!';
});



// Test admin route without middleware
Route::get('/test-admin-route', function () {
    \Log::info('Test admin route accessed', [
        'user' => \Auth::user(),
        'is_admin' => \Auth::user() ? \Auth::user()->is_admin : false
    ]);
    return 'Test admin route works!';
});

// Direct route for testing approved reports
Route::get('/approved-reports-test', [\App\Http\Controllers\Admin\DashboardController::class, 'approvedReports'])
    ->name('approved.reports.test');
