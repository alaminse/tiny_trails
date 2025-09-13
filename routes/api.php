<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;

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
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });
});


Route::middleware(['auth:sanctum'])->group(function () {

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

    Route::controller(DeviceController::class)
        ->prefix('devices')
        ->group(function () {
            // Get devices for a specific kid
            Route::get('/kid/{kidId}','getDevicesForKid');

            // CRUD operations for devices
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');

            // Device status and tracking
            Route::get('/{id}/status', 'getDeviceStatus');
            Route::post('/{id}/start-tracking', 'startTracking');
            Route::get('/{id}/location-history', 'getLocationHistory');
            Route::post('/{id}/command', 'sendCommand');



             // Manual sync with TrackSolidPro
            Route::post('/{id}/sync-tracksolid', 'syncWithTrackSolid');

            // Check TrackSolid connection status
            Route::get('/{id}/tracksolid-status', 'getTrackSolidStatus');

        });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// PayWay Subscription Routes
Route::prefix('subscriptions')->group(function () {
    // Public routes (no authentication required)
    Route::get('plans', [SubscriptionController::class, 'getPlans']);
    Route::get('publishable-key', [SubscriptionController::class, 'getPublishableKey']);

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('current', [SubscriptionController::class, 'getCurrentSubscription']);
        Route::post('create', [SubscriptionController::class, 'createSubscription']);
        Route::post('cancel', [SubscriptionController::class, 'cancelSubscription']);
        Route::post('resume', [SubscriptionController::class, 'resumeSubscription']);
        Route::get('history', [SubscriptionController::class, 'getPaymentHistory']);
    });
});

// PayWay Webhook Routes (no authentication, handled by middleware)
Route::post('payway/webhook', [WebhookController::class, 'handleWebhook'])
    ->middleware('payway.webhook');
