<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\PayWayController;
use Modules\Subscription\app\Http\Controllers\SubscriptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->name('subscriptions.')
        ->group(function () {
            Route::get('/plans', 'plans');
            // User's own subscriptions
            Route::get('/my-subscriptions', 'getUserSubscriptions')->name('my-subscriptions');
            // Subscription actions (users can manage their own)
            Route::post('/cancel/{id}', 'cancel')->name('cancel');
    });

    // PayWay public routes
    Route::controller(PayWayController::class)->prefix('payway')->name('payway.')->group(function () {
        Route::post('/create-subscription', 'storeSubscription')->name('create-subscription');
        Route::get('/connection-status', 'getApiStatus')->name('connection-status');
        Route::get('/{id}/transactions', 'getTransactionHistory')->name('transactions');
        Route::patch('/payment-method/{id}', 'updateCustomerPaymentMethod')->name('update-payment-method');
    });
});

