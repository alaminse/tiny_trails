<?php

use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\ApiDriverController;
use Modules\RideAssignment\app\Http\Controllers\ApiRideAssignController;
use Modules\RideAssignment\app\Http\Controllers\RideLocationController;


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
            Route::get('/details/{id}', 'getRideDetails');
        });


    Route::controller(ApiDriverController::class)
        ->prefix('driver')
        ->group(function () {
            // Driver schedule APIs
            Route::get('/schedule', 'schedule');
            Route::get('/schedule/date', 'getDriverDateSchedule');
            // Driver dashboard and earnings
            // Route::get('/dashboard', 'driverDashboard');
            // Route::get('/earnings', 'driverEarnings');

            // Driver actions
            Route::patch('/ride/{rideId}/status', 'updateRideStatus');
            Route::post('/ride/{rideId}/upload-photo', 'uploadPhoto');
            Route::post('/fcm-token', 'updateFcmToken');
            Route::get('/notifications', 'getNotifications');
            Route::patch('/notifications/{notificationId}/read', 'markNotificationAsRead');

        });

    Route::controller(RideLocationController::class)
        ->prefix('ride-locations')
        ->group(function () {
            // Route::get('/', 'index');
            // Route::get('/{id}','show');
            Route::post('/store', 'store');
            Route::post('/update', 'update');
            Route::get('/rides', 'getRides');
            Route::get('/ride/{ride_location}','getLiveRide');

            // Custom routes
            // Route::get('/driver/{driverId}', 'getByDriver');
            // Route::get('/parent/{parentId}', 'getByParent');
        });
});
