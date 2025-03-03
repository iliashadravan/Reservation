<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\CompleteProfileRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function completeProfile(CompleteProfileRequest $request)
    {
        $user = auth()->user();

        if ($user->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to complete this information.'
            ], 403);
        }

        $doctor = Doctor::where('user_id', $user->id)->first();

        if (!$doctor) {
            $doctor = Doctor::create([
                'user_id'        => $user->id,
                'specialty'      => $request->input('specialty'),
                'clinic_address' => $request->input('clinic_address'),
            ]);
        } else {
            $doctor->update([
                'specialty'      => $request->input('specialty'),
                'clinic_address' => $request->input('clinic_address'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your information was successfully registered.',
            'doctor'  => $doctor
        ]);
    }
}
