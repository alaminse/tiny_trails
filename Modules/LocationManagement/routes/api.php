<?php

use Illuminate\Support\Facades\Route;
use Modules\LocationManagement\Http\Controllers\LocationManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('locationmanagements', LocationManagementController::class)->names('locationmanagement');
});
