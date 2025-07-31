<?php

use Illuminate\Support\Facades\Route;
use Modules\UserRolePermission\App\Http\Controllers\UserRolePermissionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('userrolepermissions', UserRolePermissionController::class)->names('userrolepermission');
});
