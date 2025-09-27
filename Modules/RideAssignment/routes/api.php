<?php

use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\ApiDriverController;
use Modules\RideAssignment\app\Http\Controllers\ApiRideAssignController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(ApiRideAssignController::class)
        ->prefix('ride')
        ->name('ride.')
        ->group(function () {
            Route::get('/schedule', 'schedule');
                // Get specific date rides
            Route::get('/schedule/date', 'getDateSchedule');
              // Get available dates for calendar
            Route::get('/available-dates', 'getAvailableDates');
        });


    Route::controller(ApiDriverController::class)
        ->prefix('driver')
        ->group(function () {
            // Driver schedule APIs
            Route::get('/schedule', 'schedule');
            Route::get('/schedule/date', 'getDriverDateSchedule');

            // Driver actions
            Route::patch('/ride/{rideId}/status', 'updateRideStatus');

            // Driver dashboard and earnings
            Route::get('/dashboard', 'driverDashboard');
            // Route::get('/earnings', 'driverEarnings');
        });
});
