<?php

use Illuminate\Support\Facades\Route;
use Modules\File\Http\Controllers\FileController;
use Modules\File\Http\Controllers\ExcelController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('files', FileController::class)->names('file');
    Route::post('files/import/items', [ExcelController::class, 'import_items'])->name('files.import.items');
    Route::post('files/import/menu', [ExcelController::class, 'import_menu'])->name('files.import.menu');
    

});
