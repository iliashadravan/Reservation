<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthController\ForgetPasswordRequest;
use App\Http\Requests\AuthController\LoginRequest;
use App\Http\Requests\AuthController\RegisterRequest;
use App\Models\User;
use App\Service\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){

        $user = User::create([
            'firstname' => $request->get('firstname'),
            'lastname'  => $request->get('lastname'),
            'email'     => $request->get('email'),
            'phone'     => $request->get('phone'),
            'password'  => Hash::make($request->get('password')),

        ]);
        return response()->json([
            'success' => true,
            'user'    => $user,
            'massege' => 'Register successfully'
        ]);
    }
    public function login(LoginRequest $request, SmsService $smsService)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, your account is deactivated.',
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $smsService->sendSms(
            $user->phone,
            "سلام {$user->firstname}، شما در تاریخ " . now()->format('Y-m-d') . " ساعت " . now()->format('H:i') . " وارد شدید."
        );

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
    public function updateProfile(RegisterRequest $request)
    {
        $user = Auth::user();

        $data = $request->only(['firstname', 'lastname', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user,
        ]);
    }

    public function forgotPassword(ForgetPasswordRequest $request, SmsService $smsService)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found!'
            ], 404);
        }

        $newPassword = Str::random(6);
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        $smsService->sendSms($user->phone, "رمز عبور جدید شما: {$newPassword}");

        return response()->json([
            'success' => true,
            'message' => 'New password has been sent via SMS!'
        ]);
    }
}
