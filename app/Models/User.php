<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'password',
        'is_active',
        'role'
    ];

    public function generateOTP()
    {
        $this->otp_code = rand(100000, 999999);
        $this->otp_expires_at = now()->addMinutes(5);
        $this->save();

        $apiKey = "API_KEY";
        $response = Http::get("https://api.kavenegar.com/v1/$apiKey/verify/lookup.json", [
            'receptor' => $this->phone,
            'token' => $this->otp_code,
            'template' => "OTPTemplate"
        ]);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }


    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isPatient()
    {
        return $this->role === 'patient';
    }
}
