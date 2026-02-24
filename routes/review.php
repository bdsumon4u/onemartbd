<?php

use App\Http\Controllers\BackEnd\ProductReviewApprovalController;
use App\Http\Controllers\ProductReviewController;
use Illuminate\Support\Facades\Route;

// Product review submission
Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('product.review.store');

// Fetch approved reviews (lazy load)
Route::get('/products/{product}/reviews', [ProductReviewController::class, 'index'])->name('product.review.fetch');

// Admin reviews
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    Route::get('/reviews', [ProductReviewApprovalController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{id}/approve', [ProductReviewApprovalController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/reviews/{id}/reject', [ProductReviewApprovalController::class, 'reject'])->name('admin.reviews.reject');
});
