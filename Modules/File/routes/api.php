<?php

use Illuminate\Support\Facades\Route;
use Modules\File\Http\Controllers\FileController;
use Modules\File\Http\Controllers\ExcelController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('files', FileController::class)->names('file');
    Route::post('files/import', [ExcelController::class, 'import']);

});
