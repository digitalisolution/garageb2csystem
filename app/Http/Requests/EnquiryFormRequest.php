<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class EnquiryFormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = parent::rules(); // Inherit base rules (e.g., reCAPTCHA)

        // Add specific rules for password reset
            $rules['vehicle_reg'] = 'nullable|string|max:10|regex:/^[A-Za-z0-9\s]+$/';
            $rules['mileage'] = 'nullable|string|max:20';
            $rules['first_name'] = 'required|string|min:2|max:50|regex:/^[A-Za-z\s\-\']+$/';
            $rules['last_name'] = 'nullable|string|min:2|max:50|regex:/^[A-Za-z\s\-\']+$/';
            $rules['email'] = 'required|email|max:255';
            $rules['phone'] = 'nullable|string|min:10|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/';
            $rules['address'] = 'required|string|min:10|max:500';
            $rules['company'] = 'nullable|string|max:100';
            $rules['city'] = 'required|string|min:2|max:100|regex:/^[A-Za-z\s\-\']+$/';
            $rules['county'] = 'required|exists:region_county,zone_id';
            $rules['country'] = 'required|exists:countries,country_id';
            $rules['postcode'] = 'required|string|min:3|max:10|regex:/^[A-Za-z0-9\s]{3,10}$/';
            $rules['message'] = 'nullable|string|min:5|max:1000';
            $rules['selected_services'] = 'required|array|min:1';
            $rules['selected_services.*']= 'required|exists:car_services,service_id';


        return $rules;
    }

    public function messages(): array
    {
        return [
            'vehicle_reg.regex' => 'Vehicle registration must contain only letters, numbers, and spaces.',
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name must contain only letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name must contain only letters, spaces, hyphens, and apostrophes.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 10 characters long.',
            'city.required' => 'City is required.',
            'city.regex' => 'City must contain only letters, spaces, hyphens, and apostrophes.',
            'county.required' => 'County is required.',
            'county.exists' => 'Selected county is invalid.',
            'country.required' => 'Country is required.',
            'country.exists' => 'Selected country is invalid.',
            'postcode.required' => 'Postcode is required.',
            'postcode.regex' => 'Please enter a valid postcode.',
            'selected_services.required' => 'Please select at least one service.',
            'selected_services.min' => 'Please select at least one service.',
            'selected_services.*.exists' => 'One or more selected services are invalid.',
        ];
    }
}