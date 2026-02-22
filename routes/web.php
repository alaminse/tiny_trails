<?php

use App\Http\Controllers\Admin\BohDashboardController;
use App\Http\Controllers\Admin\DriverWageController;
use App\Http\Controllers\Admin\FaceVerificationController;
use App\Http\Controllers\Admin\ShiftBroadcastController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\RideAssignController;

Route::get('/', function () {
    return view('home');
});

Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
// routes/web.php

// routes/web.php  (BOH admin)
Route::post('shift-broadcast/send', [ShiftBroadcastController::class, 'broadcast'])
    ->name('admin.shift.broadcast');

// routes/api.php  (Driver App)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('shift/accept', [ShiftBroadcastController::class, 'accept'])
        ->name('driver.shift.accept');
});

Route::middleware(['auth', 'verified'])->as('admin.')->group(function () {

    // ── BoH Live Dashboard ──────────────────────────────────────────────
    Route::get('boh/dashboard', [BohDashboardController::class, 'index'])
        ->name('boh.dashboard')
        ->middleware('can:boh-dashboard');

    // ── Shift Broadcasts ────────────────────────────────────────────────
    Route::middleware('can:list-shift-broadcast')->group(function () {
        Route::get('shift-broadcast', [ShiftBroadcastController::class, 'index'])
            ->name('shift.broadcast.index');
        Route::post('shift-broadcast', [ShiftBroadcastController::class, 'store'])
            ->name('shift.broadcast.store');
        Route::patch('shift-broadcast/{broadcast}/cancel', [ShiftBroadcastController::class, 'cancel'])
            ->name('shift.broadcast.cancel');
        Route::patch('shift-broadcast/{broadcast}/extend', [ShiftBroadcastController::class, 'extend'])
            ->name('shift.broadcast.extend');
    });

    // Capacity check (AJAX — used in Ride Assign form)
    Route::post('ride-assign/check-capacity', [RideAssignController::class, 'checkCapacity'])
        ->name('ride.assign.check-capacity');

    // ── Timesheets ──────────────────────────────────────────────────────
    Route::middleware('can:list-timesheets')->group(function () {
        Route::get('timesheets', [TimesheetController::class, 'index'])
            ->name('timesheets.index');
        Route::get('timesheets/{timesheet}', [TimesheetController::class, 'show'])
            ->name('timesheets.show');
        Route::patch('timesheets/{timesheet}/approve', [TimesheetController::class, 'approve'])
            ->name('timesheets.approve');
        Route::patch('timesheets/{timesheet}/reject', [TimesheetController::class, 'reject'])
            ->name('timesheets.reject');
    });

    // ── Driver Wages ────────────────────────────────────────────────────
    Route::middleware('can:list-driver-wages')->group(function () {
        Route::get('driver-wages', [DriverWageController::class, 'index'])
            ->name('driver.wages.index');
        Route::post('driver-wages', [DriverWageController::class, 'store'])
            ->name('driver.wages.store');
        Route::put('driver-wages/{wage}', [DriverWageController::class, 'update'])
            ->name('driver.wages.update');
        Route::delete('driver-wages/{wage}', [DriverWageController::class, 'destroy'])
            ->name('driver.wages.destroy');
    });

    // ── Vehicle Types ───────────────────────────────────────────────────
    Route::middleware('can:list-vehicle-types')->group(function () {
        Route::get('vehicle-types', [VehicleTypeController::class, 'index'])
            ->name('vehicle.types.index');
        Route::post('vehicle-types', [VehicleTypeController::class, 'store'])
            ->name('vehicle.types.store');
        Route::put('vehicle-types/{vehicleType}', [VehicleTypeController::class, 'update'])
            ->name('vehicle.types.update');
        Route::patch('vehicle-types/assign', [VehicleTypeController::class, 'assignToDriver'])
            ->name('vehicle.types.assign');
        Route::delete('vehicle-types/{vehicleType}', [VehicleTypeController::class, 'destroy'])
            ->name('vehicle.types.destroy');
    });

    // ── Face Verification ───────────────────────────────────────────────
    Route::middleware('can:list-face-verification')->group(function () {
        Route::get('face-verification', [FaceVerificationController::class, 'index'])
            ->name('face.verification.index');
    });
});
// ── API Routes (Driver App) ─────────────────────────────────────────
// Add these in routes/api.php inside sanctum middleware:
//
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('driver/face-verify',       [App\Http\Controllers\Api\FaceVerificationController::class, 'verify']);
//     Route::get ('driver/face-verify/status',[App\Http\Controllers\Api\FaceVerificationController::class, 'status']);
//     Route::middleware('face.verified')->group(function () {
//         Route::post('shift/accept',          [App\Http\Controllers\Api\ShiftBroadcastController::class, 'accept']);
//         Route::post('ride/{ride}/complete',  [App\Http\Controllers\Api\RideController::class, 'complete']);
//     });
// });
