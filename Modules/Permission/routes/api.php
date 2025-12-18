<?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\Http\Controllers\PermissionController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('permissions', PermissionController::class)->names('permission');
});
