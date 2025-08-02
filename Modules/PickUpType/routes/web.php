<?php

use Illuminate\Support\Facades\Route;
use Modules\PickUpType\App\Http\Controllers\PickUpTypeController;

// 
Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {
    Route::controller(PickUpTypeController::class)
        ->prefix('pickuptypes')
        ->as('pickuptypes.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{pickuptype}', 'edit')->name('edit');
                Route::put('/update/{pickuptype}', 'update')->name('update');
                Route::delete('/delete/{pickuptype}','destroy')->name('delete');
                Route::post('/restore/{pickuptype}','restore')->name('restore');
                Route::delete('/force-delete/{pickuptype}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
            });
        });
