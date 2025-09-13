<?php

use App\Http\Controllers\Api\DeviceController;
use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\app\Http\Controllers\Api\KidController;
use Modules\UserRolePermission\app\Http\Controllers\Api\FaceController;

Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
    Route::controller(FaceController::class)
        ->prefix('faces')
        ->group(function () {
            Route::get('/verify/{driver}', 'verify');
            Route::post('/store', 'store');
            Route::get('/verification', 'verification');
        });

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

    Route::controller(DeviceController::class)
        ->prefix('kids')
        ->group(function () {
            Route::post('/{kid}/connect-device', 'connectDevice');
            Route::post('/{kid}/disconnect-device', 'disconnectDevice');
            Route::get('/{kid}/devices/status', 'getLiveStatus');
            Route::get('/devices/{imei}/status', 'getDeviceStatus');
            Route::get('/devices/{imei}/battery', 'getBatteryStatus');
            Route::post('/devices/{imei}/control', 'controlDevice');
        });
});


