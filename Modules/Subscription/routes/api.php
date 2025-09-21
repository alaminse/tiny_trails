<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\PayWayController;
use Modules\Subscription\app\Http\Controllers\SubscriptionController;

Route::middleware(['auth:sanctum'])->group(function () {
//     Route::controller(SubscriptionController::class)
//         ->prefix('subscriptions')
//         ->group(function () {
//             Route::get('/plans', 'plans');
//             Route::get('/plan/details/{plan}', 'planDetails');

//             Route::post('/buynow', 'buynow');
//             Route::get('/', 'index');
//             Route::get('/details/{subscription}', 'details');
//         });

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        // User's own subscriptions
        Route::get('/my-subscriptions', [SubscriptionController::class, 'getUserSubscriptions'])->name('my-subscriptions');
        Route::get('/{id}/transactions', [PayWayController::class, 'getTransactionHistory'])->name('transactions');

        // Subscription actions (users can manage their own)
        Route::post('/cancel/{id}', [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::patch('/payment-method/{id}', [PayWayController::class, 'updateCustomerPaymentMethod'])->name('update-payment-method');
    });

    // PayWay public routes
    Route::prefix('payway')->name('payway.')->group(function () {
        Route::post('/create-subscription', [PayWayController::class, 'storeSubscription'])->name('create-subscription');
        Route::get('/connection-status', [PayWayController::class, 'getApiStatus'])->name('connection-status');
    });
});

