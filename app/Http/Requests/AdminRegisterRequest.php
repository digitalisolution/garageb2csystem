<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRegisterRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; // Allow all users to access this request
    }

    public function rules(): array
    {
        $rules = parent::rules(); // Inherit reCAPTCHA rules from BaseFormRequest

        // Add specific rules for admin registration
        $rules += [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);
    }
}