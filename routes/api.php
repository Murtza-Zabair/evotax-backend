<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('products')->group(function () {
    Route::get('/list', [ProductController::class, 'apiIndex']); // Get all products or filter by category
    Route::get('/category/{categoryId}', [ProductController::class, 'apiByCategory']); // Get products by category ID
    Route::get('/{id}', [ProductController::class, 'apiShow']); // Get single product
});

// Category API Routes (optional)
Route::prefix('categories')->group(function () {
    Route::get('/list', [CategoryController::class, 'apiIndex']); // Get all categories
});
Route::post('/contact', [ContactController::class, 'submit']);
Route::post('/message', [ContactController::class, 'message']);
Route::get('/banner/{position}', [BannerController::class, 'getBannerByPosition']);
