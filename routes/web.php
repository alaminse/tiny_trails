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
    Route::controller(ShiftBroadcastController::class)->middleware('can:list-shift-broadcast')->group(function () {
        Route::get('shift-broadcast', 'index')
            ->name('shift.broadcast.index');
        Route::post('shift-broadcast', 'store')
            ->name('shift.broadcast.store');
        Route::patch('shift-broadcast/{broadcast}/cancel', 'cancel')
            ->name('shift.broadcast.cancel');
        Route::patch('shift-broadcast/{broadcast}/extend', 'extend')
            ->name('shift.broadcast.extend');
    });

    // Capacity check (AJAX — used in Ride Assign form)
    Route::post('ride-assign/check-capacity', [RideAssignController::class, 'checkCapacity'])
        ->name('ride.assign.check-capacity');
    // ── Timesheet ─────────────────────────────────────────
    Route::controller(TimesheetController::class)->prefix('timesheets')->name('timesheets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/export', 'export')->name('export');
        Route::get('/{id}/detail', 'detail')->name('detail');
        Route::patch('/{id}/status', 'updateStatus')->name('status');
        Route::post('/approve-all', 'approveAll')->name('approve-all');
    });

    Route::controller(TwilioCredentialController::class)->prefix('twilio')->name('twilio.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{twilioCredential}', 'update')->name('update');
        Route::post('/{twilioCredential}/activate', 'activate')->name('activate');
        Route::delete('/{twilioCredential}', 'destroy')->name('destroy');
        Route::post('/test-send', 'testSend')->name('test');
        Route::post('/validate', 'validateCredentials')->name('validate');
        
    });

    // ── Driver Wages ────────────────────────────────────────────────────
    Route::controller(DriverWageController::class)->middleware('can:list-driver-wages')->group(function () {
        Route::get('driver-wages', 'index')
            ->name('driver.wages.index');
        Route::post('driver-wages', 'store')
            ->name('driver.wages.store');
        Route::put('driver-wages/{wage}', 'update')
            ->name('driver.wages.update');
        Route::delete('driver-wages/{wage}', 'destroy')
            ->name('driver.wages.destroy');
    });

    // ── Vehicle Types ───────────────────────────────────────────────────
    Route::controller(VehicleTypeController::class)->middleware('can:list-vehicle-types')->group(function () {
        Route::get('vehicle-types', 'index')
            ->name('vehicle.types.index');
        Route::post('vehicle-types', 'store')
            ->name('vehicle.types.store');
        Route::put('vehicle-types/{vehicleType}', 'update')
            ->name('vehicle.types.update');
        Route::patch('vehicle-types/assign', 'assignToDriver')
            ->name('vehicle.types.assign');
        Route::delete('vehicle-types/{vehicleType}', 'destroy')
            ->name('vehicle.types.destroy');
    });

    // ── Face Verification ───────────────────────────────────────────────
    Route::middleware('can:list-face-verification')->group(function () {
        Route::get('face-verification', [FaceVerificationController::class, 'index'])
            ->name('face.verification.index');
    });

    Route::controller(DriverShiftController::class)->prefix('driver-shifts')->name('driver.shifts.')->group(function () {

        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{shift}', 'show')->name('show');
        Route::post('/{shift}/confirm', 'confirm')->name('confirm');
        Route::delete('/{shift}', 'destroy')->name('destroy');

        // Ride assignment
        Route::post('/{shift}/assign-ride', 'assignRide')->name('assignRide');
        Route::delete('/{shift}/remove-ride/{ride}', 'removeRide')->name('removeRide');

        // Driver management on shift
        Route::post('/{shift}/add-driver', 'addDriver')->name('addDriver');
        Route::delete('/{shift}/remove-driver/{driver}', 'removeDriver')->name('removeDriver');
    });

    Route::controller(AttendanceController::class)->prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/driver/{id}', 'driverDetail')->name('driver');
        Route::get('/export', 'export')->name('export');
    });
});

Route::controller(FrontendController::class)->name('frontend.')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/how-it-works', 'how_it_works')->name('how_it_works');
    Route::get('/pricing', 'pricing')->name('pricing');
    Route::get('/safety', 'safety')->name('safety');
    Route::get('/contact', 'contact')->name('contact');

    // Route::get('/', function () {
    //     return view('home');
});
