<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/credit', [TransactionController::class, 'credit']);
    Route::post('/debit', [TransactionController::class, 'debit']);
});

// Customers
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);

// Transactions
Route::get('/transactions/{customerId}', [TransactionController::class, 'index']);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);
