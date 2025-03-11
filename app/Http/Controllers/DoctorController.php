<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\CompleteProfileRequest;
use App\Http\Requests\PrescriptionRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{

    public function listPatients()
    {

        $user = auth()->user();
        if ($user->doctor) {
            $appointments = Appointment::where('doctor_id', $user->doctor->id)->with(['user', 'schedule'])->get();
            return response()->json($appointments);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

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
                'user_id' => $user->id,
                'specialty' => $request->input('specialty'),
                'clinic_address' => $request->input('clinic_address'),
            ]);
        } else {
            $doctor->update([
                'specialty' => $request->input('specialty'),
                'clinic_address' => $request->input('clinic_address'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your information was successfully registered.',
            'doctor' => $doctor
        ]);
    }

    public function addPrescription(PrescriptionRequest $request)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'doctor') {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
        }

        $Prescription = Prescription::create([

            'doctor_id'    => $user->doctor->id,
            'user_id'      => $request->user_id,
            'medications'  => $request->medications,
            'instructions' => $request->instructions,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your information was successfully registered.',
            'prescription' => $Prescription
        ]);
    }

    public function getUserPrescriptions()
    {
        $user = auth()->user();

        if ($user->role === 'doctor') {
            $prescriptions = Prescription::where('doctor_id', $user->doctor->id)->get();
        }

        return response()->json([
            'success' => true,
            'prescriptions' => $prescriptions,
        ]);
    }
}
