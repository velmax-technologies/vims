<?php

use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\StockController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('stocks', StockController::class)->names('stock');
});
