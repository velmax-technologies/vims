<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Http\Controllers\TagController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('tags', TagController::class)->names('tag');
});
