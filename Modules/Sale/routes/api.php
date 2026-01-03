<?php

use Illuminate\Support\Facades\Route;
use Modules\Sale\Http\Controllers\SaleController;
use Modules\Sale\Http\Controllers\ItemSalesController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('sales', SaleController::class)->names('sale');
});

// item sales
Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('item_sales', ItemSalesController::class)->names('item_sale');
});

