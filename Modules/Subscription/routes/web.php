<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\app\Http\Controllers\PlanController;
use Modules\Subscription\app\Http\Controllers\SubscriptionController;

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
                Route::get('states/by-country/{country}', 'stateGet');
                Route::get('cities/by-state/{state}', 'cityGet');
            });
});
