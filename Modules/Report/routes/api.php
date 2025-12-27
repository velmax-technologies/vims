<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('reports', ReportController::class)->names('report');
});
