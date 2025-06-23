<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class RegisterFormRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = parent::rules(); // Include rules from BaseFormRequest

        // Add registration-specific rules
        $rules['customer_name'] = 'required|string|min:2|max:50';
        $rules['customer_email'] = 'required|string|email|max:255|unique:customers,customer_email';
        $rules['password'] = ['required', Password::min(8)->letters()->numbers()->symbols(), 'confirmed'];
        $rules['company_name'] = 'required|string|max:100';
        $rules['billing_address_country'] = 'required|string';
        $rules['billing_address_street'] = 'required|string|max:100';
        $rules['billing_address_city'] = 'required|string|max:50';
        $rules['billing_address_postcode'] = 'required|string|max:10';
        $rules['billing_address_county'] = 'required|string';

        return $rules;
    }
}