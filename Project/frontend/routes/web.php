<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - CHỈ trả về View (giao diện demo, không xử lý nghiệp vụ)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
Route::get('/batches/{id}', [BatchController::class, 'show'])->name('batches.show');

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');

Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
Route::get('/devices/{id}', [DeviceController::class, 'show'])->name('devices.show');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
