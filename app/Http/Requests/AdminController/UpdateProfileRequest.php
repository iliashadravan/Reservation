<?php

namespace App\Http\Requests\AdminController;

use App\Http\Requests\Request;

class UpdateProfileRequest extends Request
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
            'firstname'  => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' .  auth()->id(),
        ];
    }
}
