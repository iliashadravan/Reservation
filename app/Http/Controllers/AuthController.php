<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthController\ForgetPasswordRequest;
use App\Http\Requests\AuthController\LoginRequest;
use App\Http\Requests\AuthController\RegisterRequest;
use App\Http\Requests\AuthController\UpdateProfileRequest;
use App\Http\Requests\AuthController\verifyOTPRequest;
use App\Models\User;
use App\Service\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'firstname' => $request->get('firstname'),
            'lastname'  => $request->get('lastname'),
            'email'     => $request->get('email'),
            'phone'     => $request->get('phone'),
            'password'  => Hash::make($request->get('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully!',
            'user'    => $user
        ], 201);
    }

    public function login(LoginRequest $request, SmsService $smsService)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'User is inactive'], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $otpCode = rand(10000, 99999);
        $user->update([
            'otp_code' => Hash::make($otpCode),
            'otp_expires_at' => now()->addMinutes(2)
        ]);

        $smsService->sendSms(
            $user->phone,
            "کد ورود شما: {$otpCode}. این کد تا 2 دقیقه معتبر است."
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your phone'
        ]);
    }
    public function verifyOTP(verifyOTPRequest $request)
    {

        $user = User::where('phone', $request->phone)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$user || !Hash::check($request->otp_code, $user->otp_code)) {
            return response()->json(['success' => false, 'message' => 'کد تأیید اشتباه است یا منقضی شده.'], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update(['otp_code' => null, 'otp_expires_at' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = $request->only(['firstname', 'lastname', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user'    => $user
        ]);
    }

    public function forgotPassword(ForgetPasswordRequest $request, SmsService $smsService)
    {
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
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
