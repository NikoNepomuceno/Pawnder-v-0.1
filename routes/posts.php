<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Post routes
    Route::get('/home', [PostController::class, 'index'])->name('home');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/share', [PostController::class, 'share'])->name('posts.share');
    Route::post('/posts/{post}/share-in-app', [PostController::class, 'shareInApp'])->name('posts.shareInApp');
    Route::get('/posts/{post}/photos', [PostController::class, 'photos'])->name('posts.photos');

    // Post reactions routes
    Route::post('/posts/{post}/reactions', [PostReactionController::class, 'store'])->name('posts.reactions.store');
    Route::delete('/posts/{post}/reactions', [PostReactionController::class, 'destroy'])->name('posts.reactions.destroy');

    // Post comments routes
    Route::get('/posts/{post}/comments', [PostCommentController::class, 'index'])->name('posts.comments.index');
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store'])->name('posts.comments.store');
    Route::put('/comments/{comment}', [PostCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [PostCommentController::class, 'destroy'])->name('comments.destroy');

    // Post report routes
    Route::post('/posts/{post}/report', [PostReportController::class, 'store'])->name('posts.report');
});
