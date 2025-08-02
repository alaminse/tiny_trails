<?php

use Illuminate\Support\Facades\Route;
use Modules\LocationManagement\App\Http\Controllers\CityController;
use Modules\LocationManagement\App\Http\Controllers\CountryController;
use Modules\LocationManagement\App\Http\Controllers\StateController;

//
Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {
    Route::controller(CountryController::class)
        ->prefix('countries')
        ->as('countries.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{country}', 'edit')->name('edit');
                Route::put('/update/{country}', 'update')->name('update');
                Route::delete('/delete/{country}','destroy')->name('delete');
                Route::post('/restore/{country}','restore')->name('restore');
                Route::delete('/force-delete/{country}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
            });

    Route::controller(StateController::class)
        ->prefix('states')
        ->as('states.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{state}', 'edit')->name('edit');
                Route::put('/update/{state}', 'update')->name('update');
                Route::delete('/delete/{state}','destroy')->name('delete');
                Route::post('/restore/{state}','restore')->name('restore');
                Route::delete('/force-delete/{state}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
            });

    Route::controller(CityController::class)
        ->prefix('cities')
        ->as('cities.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{city}', 'edit')->name('edit');
                Route::put('/update/{city}', 'update')->name('update');
                Route::delete('/delete/{city}','destroy')->name('delete');
                Route::post('/restore/{city}','restore')->name('restore');
                Route::delete('/force-delete/{city}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
            });
    });

