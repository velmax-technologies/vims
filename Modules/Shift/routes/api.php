<?php

use Illuminate\Support\Facades\Route;
use Modules\Shift\Http\Controllers\ShiftController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('shifts', ShiftController::class)->names('shift');
});
