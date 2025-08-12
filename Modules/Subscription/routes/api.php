<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\Api\SubscriptionController;

Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/store', 'store');
            Route::get('/plans', 'plans');
            Route::get('/show/{subscription}', 'show');
        });
});
