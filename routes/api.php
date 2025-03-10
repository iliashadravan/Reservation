<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PatientAppointmentController;
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
    Route::prefix('user')->group(function () {
        Route::get('/doctors', [PatientAppointmentController::class, 'listDoctors']);
        Route::get('/schedule/available-times/{schedule_id}', [PatientAppointmentController::class, 'availableTimeSlots']);
        Route::post('/appointment/reserve', [PatientAppointmentController::class, 'reserve']);
        Route::get('/appointments/my', [PatientAppointmentController::class, 'myAppointments']);
        Route::get('/my-prescriptions', [PatientAppointmentController::class, 'myPrescriptions']);
    });

    Route::prefix('doctor')->group(function () {
        Route::get('/patients', [DoctorController::class, 'listPatients']);
        Route::get('/doctor-schedules', [DoctorScheduleController::class, 'index']);
        Route::post('/doctor-schedules', [DoctorScheduleController::class, 'store']);
        Route::delete('/doctor-schedules/{id}', [DoctorScheduleController::class, 'destroy']);
        Route::post('/complete-profile', [DoctorController::class, 'completeProfile']);
        Route::post('/prescription', [DoctorController::class, 'addPrescription']);
        Route::get('/prescription', [DoctorController::class, 'getUserPrescriptions']);
    });
});
