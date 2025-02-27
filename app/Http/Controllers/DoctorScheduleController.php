<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\storeRequest;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if(!$user|| $user->role !== 'doctor'){
            return response()->json([
                'success' => false,
            ] , 403);
        }

        $schedules = DoctorSchedule::all();
        return response()->json($schedules);
    }

    public function store(storeRequest $request)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'doctor') {
            return response()->json([
                'success' => false,
            ], 403);
        }

        $schedule = DoctorSchedule::create([
            'doctor_id'   => $user->id,
            'date'       => $request->input('date'),
            'start_time' => $request->input('start_time'),
            'end_time'   => $request->input('end_time'),
        ]);

        return response()->json($schedule);
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

        return response()->json(['delete' => 'true']);
    }


}
