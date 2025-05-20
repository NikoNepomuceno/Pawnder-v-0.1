<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    // Register Routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.page');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Login Routes
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Google Auth
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification Routes
Route::get('/verify-email', [VerificationController::class, 'show'])
    ->name('verify.email.page');
Route::post('/verify-email', [VerificationController::class, 'verify'])
    ->name('verify.email');
Route::post('/resend-verification', [VerificationController::class, 'resend'])
    ->name('resend.verification');
