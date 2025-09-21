<?php

use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\RideAssignmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Ride Assignment Routes
    Route::prefix('rideassignments')->name('rideassignments.')->group(function () {

        // Standard CRUD routes
        Route::get('/', [RideAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [RideAssignmentController::class, 'create'])->name('create');
        Route::post('/store', [RideAssignmentController::class, 'store'])->name('store');
        Route::get('/show/{id}', [RideAssignmentController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [RideAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RideAssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [RideAssignmentController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/restore/{id}', [RideAssignmentController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [RideAssignmentController::class, 'forceDelete'])->name('force-delete');
        Route::get('/get-data', [RideAssignmentController::class, 'getData'])->name('get-data');

        // Status management routes
        Route::post('/accept/{id}', [RideAssignmentController::class, 'accept'])->name('accept');
        Route::post('/start/{id}', [RideAssignmentController::class, 'start'])->name('start');
        Route::post('/complete/{id}', [RideAssignmentController::class, 'complete'])->name('complete');
        Route::post('/cancel/{id}', [RideAssignmentController::class, 'cancel'])->name('cancel');
        Route::post('/mark-no-show/{id}', [RideAssignmentController::class, 'markAsNoShow'])->name('mark-no-show');

        // Additional functionality routes
        Route::get('/available-drivers', [RideAssignmentController::class, 'getAvailableDrivers'])->name('available-drivers');
        Route::post('/bulk-assign', [RideAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::post('/bulk-cancel', [RideAssignmentController::class, 'bulkCancel'])->name('bulk-cancel');
        Route::get('/search', [RideAssignmentController::class, 'search'])->name('search');
        Route::get('/stats', [RideAssignmentController::class, 'getStats'])->name('stats');

        // Driver and Parent specific routes
        Route::get('/driver/{driverId}', [RideAssignmentController::class, 'getDriverRides'])->name('driver-rides');
        Route::get('/parent/{parentId}', [RideAssignmentController::class, 'getParentRides'])->name('parent-rides');
        Route::get('/today', [RideAssignmentController::class, 'getTodaysRides'])->name('todays-rides');
    });
});

// API Routes for mobile app or AJAX calls
Route::prefix('api/v1')->name('api.')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('ride-assignments')->name('ride-assignments.')->group(function () {

        // CRUD operations
        Route::get('/', [RideAssignmentController::class, 'index'])->name('index');
        Route::post('/', [RideAssignmentController::class, 'store'])->name('store');
        Route::get('/{id}', [RideAssignmentController::class, 'show'])->name('show');
        Route::put('/{id}', [RideAssignmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [RideAssignmentController::class, 'destroy'])->name('destroy');

        // Status management
        Route::post('/{id}/accept', [RideAssignmentController::class, 'accept'])->name('accept');
        Route::post('/{id}/start', [RideAssignmentController::class, 'start'])->name('start');
        Route::post('/{id}/complete', [RideAssignmentController::class, 'complete'])->name('complete');
        Route::post('/{id}/cancel', [RideAssignmentController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/mark-no-show', [RideAssignmentController::class, 'markAsNoShow'])->name('mark-no-show');

        // Additional endpoints
        Route::get('/driver/{driverId}/rides', [RideAssignmentController::class, 'getDriverRides'])->name('driver-rides');
        Route::get('/parent/{parentId}/rides', [RideAssignmentController::class, 'getParentRides'])->name('parent-rides');
        Route::get('/today/rides', [RideAssignmentController::class, 'getTodaysRides'])->name('todays-rides');
        Route::get('/search/rides', [RideAssignmentController::class, 'search'])->name('search');
        Route::get('/available-drivers', [RideAssignmentController::class, 'getAvailableDrivers'])->name('available-drivers');
        Route::get('/statistics', [RideAssignmentController::class, 'getStats'])->name('stats');

        // Bulk operations
        Route::post('/bulk-assign', [RideAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::post('/bulk-cancel', [RideAssignmentController::class, 'bulkCancel'])->name('bulk-cancel');
    });
});

// Driver specific routes (for driver app/dashboard)
Route::prefix('driver')->name('driver.')->middleware(['auth', 'role:driver'])->group(function () {

    Route::prefix('rides')->name('rides.')->group(function () {
        Route::get('/', [RideAssignmentController::class, 'getDriverRides'])->name('index');
        Route::get('/today', [RideAssignmentController::class, 'getTodaysRides'])->name('today');
        Route::post('/{id}/accept', [RideAssignmentController::class, 'accept'])->name('accept');
        Route::post('/{id}/start', [RideAssignmentController::class, 'start'])->name('start');
        Route::post('/{id}/complete', [RideAssignmentController::class, 'complete'])->name('complete');
        Route::get('/{id}', [RideAssignmentController::class, 'show'])->name('show');
    });
});

// Parent specific routes (for parent app/dashboard)
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {

    Route::prefix('rides')->name('rides.')->group(function () {
        Route::get('/', [RideAssignmentController::class, 'getParentRides'])->name('index');
        Route::get('/today', [RideAssignmentController::class, 'getTodaysRides'])->name('today');
        Route::get('/{id}', [RideAssignmentController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [RideAssignmentController::class, 'cancel'])->name('cancel');
    });
});

// Public routes (if needed for tracking or external services)
Route::prefix('public')->name('public.')->group(function () {

    Route::prefix('rides')->name('rides.')->group(function () {
        // Only allow viewing ride details with a secure token
        Route::get('/{id}/track/{token}', [RideAssignmentController::class, 'trackRide'])->name('track');
    });
});
