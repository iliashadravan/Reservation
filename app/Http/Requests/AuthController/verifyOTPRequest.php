<?php

namespace App\Http\Requests\AuthController;

use App\Http\Requests\Request;

class verifyOTPRequest extends Request
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
            'phone'    => 'required|regex:/^09[0-9]{9}$/',
            'otp_code' => 'required|numeric',
        ];
    }
}
