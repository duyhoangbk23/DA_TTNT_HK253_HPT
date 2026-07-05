<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;

Route::middleware('api')->group(function () {
    // Products API
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'apiShow']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Categories API
    Route::get('/categories', [CategoryController::class, 'apiIndex']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'apiShow']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Inventories API
    Route::get('/inventories', [InventoryController::class, 'apiIndex']);
    Route::get('/inventories/{id}', [InventoryController::class, 'apiShow']);
    Route::patch('/inventories/{id}/adjust', [InventoryController::class, 'adjust']);
});
