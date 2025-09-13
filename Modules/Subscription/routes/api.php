<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\Api\SubscriptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->group(function () {
            Route::get('/plans', 'plans');
            Route::get('/plan/details/{plan}', 'planDetails');

            Route::post('/buynow', 'buynow');
            Route::get('/', 'index');
            Route::get('/details/{subscription}', 'details');
        });
});
