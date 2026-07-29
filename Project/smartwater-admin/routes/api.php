<?php

use App\Support\DatabaseFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TelemetryController;

Route::post('/telemetry', [TelemetryController::class, 'ingest']);

Route::get('/health/database', function () {
    try {
        DB::select('SELECT 1');

        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
        ]);
    } catch (\Throwable $exception) {
        Log::error('Database health check failed.', DatabaseFailure::context($exception));

        return response()->json([
            'status' => 'unhealthy',
            'database' => 'disconnected',
        ], 503);
    }
});

// Nhóm API điều phối request theo tài nguyên; controller giữ ranh giới HTTP với phần nghiệp vụ tương ứng.
Route::middleware('api')->group(function () {
    // Products API
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::post('/products', [ProductController::class, 'apiStore']);
    Route::get('/products/{id}', [ProductController::class, 'apiShow']);
    Route::put('/products/{id}', [ProductController::class, 'apiUpdate']);
    Route::delete('/products/{id}', [ProductController::class, 'apiDestroy']);

    // Categories API
    Route::get('/categories', [CategoryController::class, 'apiIndex']);
    Route::post('/categories', [CategoryController::class, 'apiStore']);
    Route::get('/categories/{id}', [CategoryController::class, 'apiShow']);
    Route::put('/categories/{id}', [CategoryController::class, 'apiUpdate']);
    Route::delete('/categories/{id}', [CategoryController::class, 'apiDestroy']);

    // Inventories API
    Route::get('/inventories', [InventoryController::class, 'apiIndex']);
    Route::get('/inventories/{id}', [InventoryController::class, 'apiShow']);
    Route::patch('/inventories/{id}/adjust', [InventoryController::class, 'apiAdjust']);
});
