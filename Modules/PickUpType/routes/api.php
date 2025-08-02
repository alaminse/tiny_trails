<?php

use Illuminate\Support\Facades\Route;
use Modules\PickUpType\Http\Controllers\PickUpTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pickuptypes', PickUpTypeController::class)->names('pickuptype');
});
