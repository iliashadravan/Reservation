<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Request;

class CompleteProfileRequest extends Request
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'doctor';
    }

    public function rules(): array
    {
        return [
            'specialty'      => 'required|string|max:255',
            'clinic_address' => 'required|string|max:500',
        ];
    }
}
