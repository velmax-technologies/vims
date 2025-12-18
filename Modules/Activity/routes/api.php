<?php

use Illuminate\Support\Facades\Route;
use Modules\Activity\Http\Controllers\ActivityController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('activities', ActivityController::class)->names('activity');
});
