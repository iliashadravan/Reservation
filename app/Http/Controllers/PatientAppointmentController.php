<?php

namespace App\Http\Controllers;

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

    public function availableSchedules($doctor_id)
    {
        $schedules = DoctorSchedule::where('doctor_id', $doctor_id)->get();

        return response()->json([
            'success' => true,
            'schedules' => $schedules
        ]);
    }

    public function Reserve(Request $request)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'patient') {
            return response()->json([
                'success' => false,
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }

        $request->validate([
            'schedule_id' => 'required|exists:doctor_schedules,id'
        ]);

        $schedule = DoctorSchedule::find($request->schedule_id);

        if (Appointment::where('schedule_id', $schedule->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'این زمان قبلاً رزرو شده است.']);
        }

        $appointment = Appointment::create([
            'user_id'     => $user->id,
            'doctor_id'   => $schedule->doctor_id,
            'schedule_id' => $schedule->id
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
