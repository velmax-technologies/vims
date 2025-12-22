<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('menu', MenuController::class)->names('menu');
});
