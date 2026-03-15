<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\DriverShiftApiController;
use App\Http\Controllers\Api\FaceRecognitionController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::get('/reset-password/{token}', function ($token) {
        return $token;
        // return view('auth.reset-password', ['token' => $token]);
    })->name('auth.reset-password');

    Route::controller(VerificationController::class)->prefix('verification')->group(function () {
        Route::post('/verify-phone', 'verifyPhone');
        Route::post('/send-phone-code', 'sendPhoneCode');
        Route::post('/send-email', 'sendEmailVerification');
        Route::post('/verify-email', 'verifyEmail');
        Route::post('/verify-pin', 'verifyPin');
    });

    Route::get('get/countries', [AuthController::class, 'getCountries']);
    Route::get('get/states', [AuthController::class, 'allStates']);
    Route::get('get/cities', [AuthController::class, 'allCities']);
    Route::get('get/state/{stateId}', [AuthController::class, 'getStateByCity']);
    Route::get('get/states/{country_id}', [AuthController::class, 'getStates']);
    Route::get('get/cities/{city_id}', [AuthController::class, 'getCities']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile', [AuthController::class, 'updateProfile']);



        Route::post('/face/store', [FaceRecognitionController::class, 'store']);
        Route::get('/face/my-face', [FaceRecognitionController::class, 'getMyFace']);
        Route::post('face-verify',         [FaceRecognitionController::class, 'verify']);
        Route::get('face-verify/status',   [FaceRecognitionController::class, 'status']);

    });
});


Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(DriverController::class)
        ->prefix('driver')
        ->group(function () {
            Route::get('/dashboard','dashboard');
            Route::patch('/face-verification', 'updateFaceVerification');
        });
    Route::controller(DriverShiftApiController::class)
        ->prefix('driver')
        ->group(function () {
        Route::get('/schedule',      'schedule');
        Route::get('/schedule/date', 'scheduleByDate');
    });
    // Parent Mobile API Routes
    Route::controller(ParentApiController::class)
        ->prefix('parent')
        ->group(function () {
            Route::get('/dashboard','dashboard');
            Route::get('/schedule','schedule');
            Route::get('/history','history');
            Route::post('/rides/{ride}/cancel','cancelRide');
            Route::get('/kids','kids');
            Route::get('/subscription','subscription');
        });

});
