<?php

use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\App\Http\Controllers\UserRolePermissionController;
use Modules\UserRolePermission\App\Http\Controllers\RoleController;
use Modules\UserRolePermission\App\Http\Controllers\PermissionController;

// middleware(['auth', 'verified'])->
Route::as('admin.')->group(function () {
    Route::controller(UserRolePermissionController::class)
        ->prefix('users')
        ->as('users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            // Route::get('/status/{assessment}', 'status')->name('status');
            // Route::get('/edit/{assessment}', 'edit')->name('edit');
            // Route::post('/update/{assessment}', 'update')->name('update');
            // // Route::delete('/destroy/{slider}', 'destroy')->name('destroy');
            // Route::get('/report', 'report')->name('report');
            // Route::get('/testbattaries', 'testbattaries')->name('testbattaries');
            // Route::post('/note', 'note')->name('note');
            // Route::get('/details', 'details')->name('details');
            // Route::get('/feedback', 'getFeedback')->name('feedback');

            // Route::get('/prescription', 'prescription')->name('prescription');
            // Route::post('/prescription/store', 'prescription_store')->name('prescription.store');
            // Route::get('/prescription/print/{slug}', 'prescription_print')->name('prescription.print');
            // Route::post('/report/upload', 'report_upload')->name('report.upload');
            // Route::get('/search-doses', 'searchDoses');
            // Route::post('/add/history', 'add_history')->name('add.history');
        });

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
                Route::get('/get/data', 'getData');
            });

});
