<?php

use Illuminate\Support\Facades\Route;
use Modules\DriverCommission\app\Http\Controllers\DriverCommissionController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Driver Commission Routes
    Route::prefix('driver-commissions')->name('driver-commissions.')->group(function () {
        
        // Main routes
        Route::get('/', [DriverCommissionController::class, 'index'])
            ->name('index');
        
        Route::get('/get-data', [DriverCommissionController::class, 'getData'])
            ->name('get-data');
        
        Route::post('/', [DriverCommissionController::class, 'store'])
            ->name('store');
        
        Route::get('/{driverCommission}', [DriverCommissionController::class, 'show'])
            ->name('show');
        
        Route::put('/{driverCommission}', [DriverCommissionController::class, 'update'])
            ->name('update');
        
        Route::delete('/{driverCommission}', [DriverCommissionController::class, 'destroy'])
            ->name('destroy');
        
        // Payment management routes
        Route::post('/{driverCommission}/mark-as-paid', [DriverCommissionController::class, 'markAsPaid'])
            ->name('mark-as-paid');
        
        Route::post('/{driverCommission}/mark-as-failed', [DriverCommissionController::class, 'markAsFailed'])
            ->name('mark-as-failed');
        
        // Bulk operations
        Route::post('/bulk-update-payment', [DriverCommissionController::class, 'bulkUpdatePayment'])
            ->name('bulk-update-payment');
        
        // Export functionality
        Route::post('/export', [DriverCommissionController::class, 'export'])
            ->name('export');
        
        Route::get('/download/{filename}', [DriverCommissionController::class, 'download'])
            ->name('download');
    });
    
    // Alternative routes for different naming conventions
    Route::prefix('drivercommissions')->name('drivercommissions.')->group(function () {
        Route::get('/get-data', [DriverCommissionController::class, 'getData'])
            ->name('get-data');
    });
});

