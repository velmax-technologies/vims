<?php

use Illuminate\Support\Facades\Route;
use Modules\StockAdjustment\Http\Controllers\StockAdjustmentController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('stock_adjustments', StockAdjustmentController::class)->names('stockadjustment');
});
