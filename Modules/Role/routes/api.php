<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('roles', RoleController::class)->names('role');
});
