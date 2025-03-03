<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\StoreRequest;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $doctor = $user->doctor;
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your information'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'schedules' => $doctor->schedules
        ]);
    }



    public function store(StoreRequest $request)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $doctor = Doctor::where('user_id', $user->id)->first();
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your information'
            ], 400);
        }

        $schedule = DoctorSchedule::create([
            'doctor_id'  => $doctor->id,
            'date'       => $request->input('date'),
            'start_time' => $request->input('start_time'),
            'end_time'   => $request->input('end_time'),
        ]);

        return response()->json([
            'success' => true,
            'schedule' => $schedule
        ]);
    }


    public function destroy(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'doctor') {
            return response()->json([
                'delete' => false,
            ], 403);
        }

        $id = $request->input('id');

        $schedule = DoctorSchedule::where('id', $id)->where('doctor_id', $user->id)->first();

        if (!$schedule) {
            return response()->json([
                'delete' => false,
            ], 404);
        }

        $schedule->delete();

        return response()->json(['delete' => true]);
    }
}
