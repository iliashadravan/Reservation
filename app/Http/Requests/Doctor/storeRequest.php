<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;


class storeRequest extends Request
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
            'date' => 'required|date|after_or_equal:today',
            'start_time' => [
                'required',
                'date_format:H:i',
                Rule::unique('doctor_schedules', 'start_time')
                    ->where(function ($query) {
                        return $query->where('doctor_id', auth()->id());
                    })->where('date', request('date'))
            ],
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];

    }
}
