<?php

use Illuminate\Support\Facades\Route;
use Modules\StockAdjustment\Http\Controllers\StockAdjustmentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('stockadjustments', StockAdjustmentController::class)->names('stockadjustment');
});
