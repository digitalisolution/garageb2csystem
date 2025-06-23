<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetFormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        // Allow all users to access this request
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules(); // Inherit base rules (e.g., reCAPTCHA)

        // Add specific rules for password reset
        $rules['email'] = 'required|string|email|max:255';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
        ];
    }
}