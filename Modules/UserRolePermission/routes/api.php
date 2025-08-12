<?php

use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\app\Http\Controllers\Api\KidController;

Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
    Route::controller(KidController::class)
        ->prefix('kids')
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/store', 'store');
            Route::get('/edit/{kid}', 'edit');
            Route::post('/update/{kid}', 'update');
            Route::get('/show/{kid}', 'show');
            Route::delete('/delete/{kid}','destroy');
        });
});
