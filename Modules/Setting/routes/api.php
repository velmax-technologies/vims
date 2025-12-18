<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('settings', SettingController::class)->names('setting');
});
