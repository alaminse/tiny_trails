<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\ParentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    
    // Driver Mobile API Routes
    Route::prefix('driver')->group(function () {
        Route::get('/dashboard', [DriverApiController::class, 'dashboard']);
        Route::get('/schedule', [DriverApiController::class, 'schedule']);
        Route::get('/earnings', [DriverApiController::class, 'earnings']);
        Route::post('/rides/{ride}/status', [DriverApiController::class, 'updateRideStatus']);
        Route::get('/profile', [DriverApiController::class, 'profile']);
        Route::put('/profile', [DriverApiController::class, 'updateProfile']);
    });

    // Parent Mobile API Routes
    Route::prefix('parent')->group(function () {
        Route::get('/dashboard', [ParentApiController::class, 'dashboard']);
        Route::get('/schedule', [ParentApiController::class, 'schedule']);
        Route::get('/history', [ParentApiController::class, 'history']);
        Route::post('/rides/{ride}/cancel', [ParentApiController::class, 'cancelRide']);
        Route::get('/kids', [ParentApiController::class, 'kids']);
        Route::get('/subscription', [ParentApiController::class, 'subscription']);
    });
});