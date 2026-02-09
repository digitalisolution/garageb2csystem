<?php

namespace App\Http\Requests;

class GarageLoginFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $rules = parent::rules(); // Inherit base rules (e.g., reCAPTCHA)

        // Add login-specific rules
        $rules['garage_email'] = 'required|string|email';
        $rules['password'] = 'required|string|min:8';

        return $rules;
    }
}