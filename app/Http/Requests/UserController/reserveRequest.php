<?php

namespace App\Http\Requests\UserController;

use App\Http\Requests\Request;

class reserveRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'schedule_id'      => 'required|exists:doctor_schedules,id',
            'appointment_time' => 'required|date_format:H:i'
        ];
    }
}
