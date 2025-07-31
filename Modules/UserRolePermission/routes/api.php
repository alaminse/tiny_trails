<?php

use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\App\Http\Controllers\UserRolePermissionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('userrolepermissions', UserRolePermissionController::class)->names('userrolepermission');
});
