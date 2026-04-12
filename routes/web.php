<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BohDashboardController;
use App\Http\Controllers\Admin\DriverShiftController;
use App\Http\Controllers\Admin\DriverWageController;
use App\Http\Controllers\Admin\FaceVerificationController;
use App\Http\Controllers\Admin\ShiftBroadcastController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\Admin\TwilioCredentialController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Modules\RideAssignment\app\Http\Controllers\RideAssignController;



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
        ->middleware('can:view-boh-dashboard');

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

    Route::prefix('twilio')->name('twilio.')->group(function () {
        Route::get('/',                             [TwilioCredentialController::class, 'index'])               ->name('index');
        Route::post('/',                            [TwilioCredentialController::class, 'store'])               ->name('store');
        Route::put('/{twilioCredential}',           [TwilioCredentialController::class, 'update'])              ->name('update');
        Route::post('/{twilioCredential}/activate', [TwilioCredentialController::class, 'activate'])            ->name('activate');
        Route::delete('/{twilioCredential}',        [TwilioCredentialController::class, 'destroy'])             ->name('destroy');
        Route::post('/test-send',                   [TwilioCredentialController::class, 'testSend'])            ->name('test');
        Route::post('/validate',                    [TwilioCredentialController::class, 'validateCredentials']) ->name('validate');
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



    Route::name('frontend.')->group(function () {
        Route::get('/',           [FrontendController::class, 'home'])->name('home');
        Route::get('/how-it-works',           [FrontendController::class, 'how_it_works'])->name('how_it_works');
        Route::get('/pricing',           [FrontendController::class, 'pricing'])->name('pricing');
        Route::get('/safety',           [FrontendController::class, 'safety'])->name('safety');
        Route::get('/contact',           [FrontendController::class, 'contact'])->name('contact');

        // Route::get('/', function () {
        //     return view('home');
    });
