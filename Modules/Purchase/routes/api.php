<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('purchases', PurchaseController::class)->names('purchase');
});
