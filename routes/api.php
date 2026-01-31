<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsageController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // Plans
    Route::get('/plans', [PlanController::class, 'index']);

    // Subscription
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription', [SubscriptionController::class, 'current']);

    // Usage
    Route::post('/usage/consume', [UsageController::class, 'consume']);
    Route::get('/usage/stats', [UsageController::class, 'stats']);
});