<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplier\Http\Controllers\SupplierController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('suppliers', SupplierController::class)->names('supplier');
});
