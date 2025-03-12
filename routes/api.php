<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Middleware\CheckIsAdmin;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/reset-password', [AuthController::class, 'forgotPassword']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('admin')->middleware([CheckIsAdmin::class])->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('', [AdminController::class, 'index']);
            Route::get('/{user}', [AdminController::class, 'show']);
            Route::post('/set-role', [AdminController::class, 'setRole']);
            Route::patch('/{user}', [AdminController::class, 'updateProfile']);
            Route::delete('/{user}', [AdminController::class, 'destroy']);
            Route::patch('/toggle-active/{user}', [AdminController::class, 'toggleActive']);
        });
    });

    Route::prefix('user')->group(function () {
        Route::get('/doctors', [PatientAppointmentController::class, 'listDoctors']);
        Route::get('/schedule/available-times/{doctor_id}/{date}', [PatientAppointmentController::class, 'availableTimeSlots']);
        Route::post('/appointment/reserve', [PatientAppointmentController::class, 'reserve']);
        Route::get('/appointments/my', [PatientAppointmentController::class, 'myAppointments']);
        Route::get('/my-prescriptions', [PatientAppointmentController::class, 'myPrescriptions']);
    });

    Route::prefix('doctor')->group(function () {
        Route::prefix('diseases')->group(function () {
            Route::get('/diseases', [DiseaseController::class, 'index']);
            Route::post('/diseases', [DiseaseController::class, 'store']);
            Route::get('/diseases/{disease}', [DiseaseController::class, 'show']);
            Route::put('/diseases/{disease}', [DiseaseController::class, 'update']);
            Route::delete('/diseases/{disease}', [DiseaseController::class, 'destroy']);
        });
        Route::get('/patients', [DoctorController::class, 'listPatients']);
        Route::get('/doctor-schedules', [DoctorScheduleController::class, 'index']);
        Route::post('/doctor-schedules', [DoctorScheduleController::class, 'store']);
        Route::delete('/doctor-schedules/{id}', [DoctorScheduleController::class, 'destroy']);
        Route::post('/complete-profile', [DoctorController::class, 'completeProfile']);
        Route::prefix('prescriptions')->group(function () {
            Route::get('/', [DoctorController::class, 'listPrescriptions']);
            Route::post('/', [DoctorController::class, 'addPrescription']);
            Route::get('/{prescription}', [DoctorController::class, 'getPrescription']);
            Route::delete('/{prescription}', [DoctorController::class, 'deletePrescription']);
        });

        });
});
