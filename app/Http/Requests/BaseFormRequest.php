<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use App\Models\MetaSettings;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to access this request
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        // Fetch reCAPTCHA settings
        $recaptchaSettings = MetaSettings::whereIn('name', [
            'google_recaptcha_site_key',
            'google_recaptcha_secret_key',
            'google_recaptcha_status'
        ])->pluck('content', 'name');

        $recaptchaStatus = $recaptchaSettings['google_recaptcha_status'] ?? 'inactive';

        // Add reCAPTCHA rule if active
        if ($recaptchaStatus === 'active') {
            $rules['g-recaptcha-response'] = 'required';
        }

        return $rules;
    }

    /**
     * Validate the reCAPTCHA response with Google.
     */
    protected function validateRecaptcha(string $recaptchaResponse): void
    {
        $recaptchaSettings = MetaSettings::whereIn('name', [
            'google_recaptcha_secret_key',
            'google_recaptcha_status'
        ])->pluck('content', 'name');

        $secretKey = $recaptchaSettings['google_recaptcha_secret_key'] ?? null;

        if (!$secretKey) {
            throw new \Exception('Google reCAPTCHA secret key is not configured.');
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
        ]);

        $recaptchaData = $response->json();

        if (!$recaptchaData['success']) {
            throw new \Exception('Google reCAPTCHA verification failed. Please try again.');
        }
        
         if (isset($recaptchaData['score'])) {
        if ($recaptchaData['score'] < 0.5) {
            throw new \Exception('reCAPTCHA v3 score too low. Suspected bot.');
        }
    }
    }
    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA to proceed.',
        ];
    }
}