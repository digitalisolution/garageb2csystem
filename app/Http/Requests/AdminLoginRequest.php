<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules += [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters long.',
        ]);
    }
}