<?php

use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\app\Http\Controllers\KidController;
use Modules\UserRolePermission\app\Http\Controllers\UserController;
use Modules\UserRolePermission\app\Http\Controllers\RoleController;
use Modules\UserRolePermission\app\Http\Controllers\PermissionController;

Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {
    Route::controller(UserController::class)
        ->prefix('users')
        ->as('users.')
        ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                Route::get('/edit/{user}', 'edit')->name('edit');
                Route::get('/show/{user}', 'show')->name('show');
                Route::put('/update/{user}', 'update')->name('update');
                Route::delete('/delete/{user}','destroy')->name('delete');
                Route::post('/restore/{user}','restore')->name('restore');
                Route::delete('/force-delete/{user}','forceDelete')->name('forceDelete');
                Route::get('/get/data', 'getData')->name('data');
                Route::get('states/by-country/{country}', 'stateGet');
                Route::get('cities/by-state/{state}', 'cityGet');
            });

    Route::get('/drivers', [UserController::class, 'index'])->name('drivers.index');
    Route::get('/parents', [UserController::class, 'index'])->name('parents.index');

    Route::controller(RoleController::class)
        ->prefix('roles')
        ->as('roles.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{role}', 'edit')->name('edit');
            Route::put('/update/{role}', 'update')->name('update');
            Route::delete('/delete/{role}','destroy')->name('delete');
            Route::post('/restore/{role}','restore')->name('restore');
            Route::delete('/force-delete/{role}','forceDelete')->name('forceDelete');
            Route::get('/get/data', 'getData')->name('data');
        });

    Route::controller(PermissionController::class)
        ->prefix('permissions')
        ->as('permissions.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{permission}', 'edit')->name('edit');
            Route::put('/update/{permission}', 'update')->name('update');
            Route::delete('/delete/{permission}','destroy')->name('delete');
            Route::post('/restore/{permission}','restore')->name('restore');
            Route::delete('/force-delete/{permission}','forceDelete')->name('forceDelete');
            Route::get('/get/data', 'getData')->name('data');
        });

    Route::controller(KidController::class)
        ->prefix('kids')
        ->as('kids.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/edit/{kid}', 'edit')->name('edit');
            Route::put('/update/{kid}', 'update')->name('update');
            Route::get('/show/{kid}', 'show')->name('show');
            Route::delete('/delete/{kid}','destroy')->name('delete');
            Route::post('/restore/{kid}','restore')->name('restore');
            Route::delete('/force-delete/{kid}','forceDelete')->name('forceDelete');
            Route::get('/get/data', 'getData')->name('data');
            Route::get('/parents', 'parents')->name('parents');
        });
});
