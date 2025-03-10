<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'clinic_address',
        'doctor_id',

    ];
    protected $casts = [
        'available_times' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
    public function patient()
    {
        return $this->hasMany(Prescription::class);
    }

}

