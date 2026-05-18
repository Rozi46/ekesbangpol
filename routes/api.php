<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{SistemController,ActionController};
use App\Http\Controllers\Api\AuthController;

/* AUTH (JWT) */
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

});


/* PROTECTED API */

Route::middleware(['auth:api'])->group(function () {

    /* DASHBOARD */
    Route::get('/dashboard', [SistemController::class, 'dash']);

    /* MENU */
    Route::get('/menu', [SistemController::class, 'menu']);

});