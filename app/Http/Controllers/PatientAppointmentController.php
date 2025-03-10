<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserController\reserveRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        date_default_timezone_set('Asia/Tehran');
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
        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'برنامه پزشک یافت نشد.'], 404);
        }

        DB::beginTransaction();
        try {
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

            DB::commit();
            return response()->json(['success' => true, 'appointment' => $appointment]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'خطایی رخ داد.'], 500);
        }
    }

    public function myAppointments()
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'patient') {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
        }

        $appointments = Appointment::where('user_id', $user->id)
            ->with(['doctor.user:id,name', 'schedule:id,start_time,end_time'])
            ->select('id', 'doctor_id', 'schedule_id', 'appointment_time')
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }
    public function myPrescriptions()
    {
        $user = auth()->user();

        $prescriptions = Prescription::where('user_id', $user->id)
            ->get(['medications', 'instructions']);

        return response()->json([
            'success' => true,
            'prescriptions' => $prescriptions
        ]);
    }

}
