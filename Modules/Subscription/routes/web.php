<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\PayWayController;
use Modules\Subscription\app\Http\Controllers\PlanController;
use Modules\Subscription\app\Http\Controllers\SubscriptionController;
use Modules\Subscription\app\Http\Controllers\SubscriptionHelperController;

Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {
    Route::controller(PlanController::class)
        ->prefix('plans')
        ->as('plans.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{plan}', 'edit')->name('edit');
                Route::get('/show/{plan}', 'show')->name('show');
                Route::put('/update/{plan}', 'update')->name('update');
                Route::delete('/delete/{plan}','destroy')->name('delete');
                Route::post('/restore/{plan}','restore')->name('restore');
                Route::delete('/force-delete/{plan}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
                Route::get('states/by-country/{country}', 'stateGet');
                Route::get('cities/by-state/{state}', 'cityGet');
                Route::post('/duplicate/{plan}','duplicate')->name('duplicate');
                Route::get('/stats', 'getStats')->name('stats');
            });

    Route::controller(SubscriptionController::class)
        ->prefix('subscriptions')
        ->as('subscriptions.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{subscription}', 'edit')->name('edit');
                Route::get('/show/{subscription}', 'show')->name('show');
                Route::put('/update/{subscription}', 'update')->name('update');
                Route::delete('/delete/{subscription}','destroy')->name('delete');
                Route::post('/restore/{subscription}','restore')->name('restore');
                Route::delete('/force-delete/{subscription}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
                Route::get('/cancel/{subscription}', 'cancel')->name('cancel');
                Route::get('states/by-country/{country}', 'stateGet');
                Route::get('cities/by-state/{state}', 'cityGet');
        });

    Route::prefix('admin')->as('admin.')->group(function () {
        // Main subscription routes

        Route::controller(SubscriptionController::class)
            ->prefix('subscriptions')
            ->as('subscriptions.')
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            // Add this route to your existing admin routes
            Route::get('/create', 'create')->name('create');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');

            // Subscription actions
            Route::post('/cancel/{id}', 'cancel')->name('cancel');
            Route::post('/reactivate/{id}', 'reactivate')->name('reactivate');

            // Data and API routes
            Route::get('/data/get', 'getData')->name('data');
            Route::get('/search', 'search')->name('search');

            // User and plan specific
            Route::get('/user/{userId}', 'getUserSubscriptions')->name('user');
            Route::get('/plan/{planId}', 'getPlanSubscriptions')->name('plan');

            // Location routes for address forms
            Route::get('/states/{country}', 'stateGet')->name('states.get');
            Route::get('/cities/{state}', 'cityGet')->name('cities.get');
        });

        Route::controller(SubscriptionHelperController::class)
            ->prefix('subscriptions')
            ->as('subscriptions.')
            ->group(function () {
                Route::get('/stats', 'getSubscriptionStats')->name('stats');

                // Helper routes for dashboard
                Route::get('/expiring', 'getExpiringSubscriptions')->name('expiring');
                Route::get('/payment-issues', 'getPaymentIssues')->name('payment-issues');
                Route::get('/recent-activity', 'getRecentActivity')->name('recent-activity');

                // Payment processing
                Route::post('/process-payment/{id}', 'processPayment')->name('process-payment');

                // Export functionality
                Route::get('/export', 'exportSubscriptions')->name('export');
        });

        // PayWay integration routes
        Route::prefix('payway')->name('payway.')->group(function () {
            // Connection and testing
            Route::get('/test-connection', [PayWayController::class, 'testConnection'])->name('test.connection');
            Route::post('/test-payment', [PayWayController::class, 'testPayment'])->name('test.payment');
            Route::get('/test-step-by-step', [PayWayController::class, 'testStepByStep'])->name('test.step-by-step');
            Route::get('/test-card-scenarios', [PayWayController::class, 'testCardScenarios'])->name('test.card-scenarios');
            Route::get('/debug-config', [PayWayController::class, 'debugConfig'])->name('debug.config');
            Route::get('/api-status', [PayWayController::class, 'getApiStatus'])->name('api.status');
            Route::delete('/cleanup-test-data', [PayWayController::class, 'cleanupTestData'])->name('cleanup.test');

            // Subscription management
            Route::post('/subscription/create', [PayWayController::class, 'storeSubscription'])->name('subscription.store');
            Route::post('/subscription/payment', [PayWayController::class, 'processSubscriptionPayment'])->name('subscription.payment');
            Route::patch('/subscription/cancel', [PayWayController::class, 'cancelSubscription'])->name('subscription.cancel');
            Route::patch('/subscription/payment-method', [PayWayController::class, 'updateCustomerPaymentMethod'])->name('subscription.update.payment');

            // Transaction and customer management
            Route::get('/subscription/{id}/transactions', [PayWayController::class, 'getTransactionHistory'])->name('subscription.transactions');
            Route::get('/customer/{customerNumber}', [PayWayController::class, 'getCustomerPaymentMethods'])->name('customer.details');
        });

        Route::post('/subscriptions/create-with-payway', [PayWayController::class, 'storeSubscription'])->name('subscriptions.create.payway');
    });
});
