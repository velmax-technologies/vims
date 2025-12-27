<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('users', UserController::class)->names('user');
});

Route::get('users', [UserController::class, 'index'])->name('users.list');
