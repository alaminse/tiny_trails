<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BohDashboardController;
use App\Http\Controllers\Admin\DriverShiftController;
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
        // ── Timesheet ─────────────────────────────────────────
    Route::prefix('timesheets')->name('timesheets.')->group(function () {
        Route::get('/',               [TimesheetController::class, 'index'])->name('index');
        Route::get('/export',         [TimesheetController::class, 'export'])->name('export');
        Route::get('/{id}/detail',    [TimesheetController::class, 'detail'])->name('detail');
        Route::patch('/{id}/status',  [TimesheetController::class, 'updateStatus'])->name('status');
        Route::post('/approve-all',   [TimesheetController::class, 'approveAll'])->name('approve-all');
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


    Route::prefix('driver-shifts')->name('driver.shifts.')->group(function () {

        Route::get('/',                             [DriverShiftController::class, 'index'])        ->name('index');
        Route::get('/create',                       [DriverShiftController::class, 'create'])       ->name('create');
        Route::post('/',                            [DriverShiftController::class, 'store'])        ->name('store');
        Route::get('/{shift}',                      [DriverShiftController::class, 'show'])         ->name('show');
        Route::post('/{shift}/confirm',             [DriverShiftController::class, 'confirm'])      ->name('confirm');
        Route::delete('/{shift}',                   [DriverShiftController::class, 'destroy'])      ->name('destroy');

        // Ride assignment
        Route::post('/{shift}/assign-ride',         [DriverShiftController::class, 'assignRide'])   ->name('assignRide');
        Route::delete('/{shift}/remove-ride/{ride}',[DriverShiftController::class, 'removeRide'])   ->name('removeRide');

        // Driver management on shift
        Route::post('/{shift}/add-driver',          [DriverShiftController::class, 'addDriver'])    ->name('addDriver');
        Route::delete('/{shift}/remove-driver/{driver}', [DriverShiftController::class, 'removeDriver'])->name('removeDriver');
    });


    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',           [AttendanceController::class, 'index'])->name('index');
        Route::get('/driver/{id}',[AttendanceController::class, 'driverDetail'])->name('driver');
        Route::get('/export',     [AttendanceController::class, 'export'])->name('export');
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
