<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PrescriptionRequest extends Request
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
            'user_id'            => 'required|exists:users,id',
            'medications'        => 'required|string',
            'instructions'       => 'nullable|string',
            'medication_times'   => 'required|array',
            'medication_times.*' => 'required|string',
            'interval'           => 'nullable|integer',
        ];
    }
}
