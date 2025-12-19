<?php

use Illuminate\Support\Facades\Route;
use Modules\Item\Http\Controllers\ItemController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('items', ItemController::class)->names('item');
});
