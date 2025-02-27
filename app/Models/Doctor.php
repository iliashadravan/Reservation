<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'clinic_address'
    ];
    protected $casts = [
        'available_times' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
