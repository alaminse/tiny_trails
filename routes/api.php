<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });
});


Route::middleware(['auth:sanctum'])->group(function () {

    // Parent Mobile API Routes
    Route::controller(ParentApiController::class)
        ->prefix('parent')
        ->group(function () {
            Route::get('/dashboard','dashboard');
            Route::get('/schedule','schedule');
            Route::get('/history','history');
            Route::post('/rides/{ride}/cancel','cancelRide');
            Route::get('/kids','kids');
            Route::get('/subscription','subscription');
        });

});
