<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckIsAdmin;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/reset-password', [AuthController::class, 'forgotPassword']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('admin')->middleware([CheckIsAdmin::class])->group(function () {
        Route::get('', [AdminController::class, 'index']);
        Route::post('/set-role', [AdminController::class, 'setRole']);
        Route::post('/delete-user', [AdminController::class, 'destroy']);
        Route::post('update-profile', [AdminController::class, 'updateProfile']);


    });
});
