<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserController\reserveRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class PatientAppointmentController extends Controller
{
    public function listDoctors()
    {
        $doctors = Doctor::with('user')->get();

        return response()->json([
            'success' => true,
            'doctors' => $doctors
        ]);
    }

    public function availableTimeSlots($schedule_id)
    {
        $schedule = DoctorSchedule::find($schedule_id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'برنامه‌ای یافت نشد.'], 404);
        }

        $start = strtotime($schedule->start_time);
        $end = strtotime($schedule->end_time);
        $interval = 30 * 60;

        $timeSlots = [];
        for ($time = $start; $time < $end; $time += $interval) {
            $formattedTime = date('H:i', $time);

            $isBooked = Appointment::where('schedule_id', $schedule->id)
                ->where('appointment_time', $formattedTime)
                ->exists();

            $timeSlots[] = [
                'time' => $formattedTime,
                'available' => !$isBooked
            ];
        }

        return response()->json([
            'success' => true,
            'time_slots' => $timeSlots
        ]);
    }


    public function reserve(reserveRequest $request)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'patient') {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }


        $schedule = DoctorSchedule::find($request->schedule_id);

        if (Appointment::where('schedule_id', $schedule->id)
            ->where('appointment_time', $request->appointment_time)
            ->exists()) {
            return response()->json(['success' => false, 'message' => 'این زمان قبلاً رزرو شده است.']);
        }

        $appointment = Appointment::create([
            'user_id'          => $user->id,
            'doctor_id'        => $schedule->doctor_id,
            'schedule_id'      => $schedule->id,
            'appointment_time' => $request->appointment_time
        ]);

        return response()->json(['success' => true, 'appointment' => $appointment]);
    }


    public function myAppointments()
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'patient') {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
        }

        $appointments = Appointment::where('user_id', $user->id)
            ->with(['doctor.user', 'schedule'])
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }
}
