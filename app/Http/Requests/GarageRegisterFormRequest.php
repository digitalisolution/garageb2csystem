<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class GarageRegisterFormRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = parent::rules(); // Include rules from BaseFormRequest

        // Add registration-specific rules for garage
        $rules['garage_name'] = 'required|string|min:2|max:50';
        $rules['garage_last_name'] = 'nullable|string|min:2|max:50';
        $rules['garage_email'] = 'required|string|email|max:255|unique:garages,garage_email';
        $rules['password'] = ['required', Password::min(8)->letters()->numbers()->symbols(), 'confirmed'];
        $rules['garage_mobile'] = 'required|string|max:15';
        $rules['garage_phone'] = 'nullable|string|max:15';
        $rules['company_name'] = 'nullable|string|max:100';
        $rules['garage_street'] = 'required|string|max:100';
        $rules['garage_city'] = 'required|string|max:50';
        $rules['garage_postcode'] = 'nullable|string|max:10';
        $rules['garage_zone'] = 'nullable|string|max:50';
        $rules['garage_country'] = 'nullable|string|max:50';
        $rules['garage_website_url'] = 'nullable|url|max:255';
        $rules['garage_opening_time'] = 'nullable|string|max:255';
        $rules['garage_logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        $rules['garage_order_types'] = 'nullable|array';
        $rules['password'] = ['required', Password::min(8)->letters()->numbers()->symbols(), 'confirmed'];
        $rules['garage_order_types.*'] = 'string|in:fully_fitted,mobile_fitted,mailorder,delivery,collection';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'garage_name.required' => 'First name is required.',
            'garage_name.min' => 'First name must be at least 2 characters.',
            'garage_email.required' => 'Email address is required.',
            'garage_email.email' => 'Please enter a valid email address.',
            'garage_email.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'garage_mobile.required' => 'Mobile number is required.',
            'garage_street.required' => 'Street address is required.',
            'garage_city.required' => 'City is required.',
            'garage_logo.image' => 'Logo must be an image file.',
            'garage_logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'garage_logo.max' => 'Logo may not be greater than 2MB.',
        ];
    }
}