<?php

use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\RideAssignController;

Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {
    Route::controller(RideAssignController::class)
        ->prefix('ride/assign')
        ->as('ride.assign.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get/data', 'getData')->name('data');
            Route::get('/subscriptions', 'subscriptions')->name('subscriptions');
            Route::get('/get/subscriptions', 'getSubscriptions')->name('get.subscriptions');
            Route::get('/create/{subscription}', 'create')->name('create');
            // Route::post('/store/{subscription}', 'store')->name('store');
            Route::get('/edit/{subscription}', 'edit')->name('edit');
            Route::get('/show/{subscription}', 'show')->name('show');

            Route::delete('/destroy/{subscription}', 'destroy')->name('destroy');
            Route::get('/api/kids/{id}', 'getKidInfo');
            Route::get('/api/locations/{id}', 'getLocationInfo');
            Route::post('ride-assign/{subscription}/store', 'rideAssignStore')
                ->name('store');
        });
});
