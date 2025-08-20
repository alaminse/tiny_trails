<?php

use Illuminate\Support\Facades\Route;
use Modules\DriverCommission\app\Http\Controllers\Api\DriverCommissionController;

Route::middleware(['auth:sanctum'])->prefix('api/admin')->name('api.admin.')->group(function () {
    
    Route::prefix('driver-commissions')->name('driver-commissions.')->group(function () {
        Route::get('/', [DriverCommissionController::class, 'getData'])
            ->name('index');
        
        Route::post('/', [DriverCommissionController::class, 'store'])
            ->name('store');
        
        Route::get('/{driverCommission}', [DriverCommissionController::class, 'show'])
            ->name('show');
        
        Route::put('/{driverCommission}', [DriverCommissionController::class, 'update'])
            ->name('update');
        
        Route::delete('/{driverCommission}', [DriverCommissionController::class, 'destroy'])
            ->name('destroy');
        
        Route::post('/{driverCommission}/payment', [DriverCommissionController::class, 'updatePayment'])
            ->name('update-payment');
        
        Route::post('/bulk-payment', [DriverCommissionController::class, 'bulkUpdatePayment'])
            ->name('bulk-payment');
    });
});