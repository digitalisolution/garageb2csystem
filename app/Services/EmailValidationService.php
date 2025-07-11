<?php

namespace App\Services;

use Verifalia\VerifaliaRestClient;
use App\Models\VerifyEmail;
use Illuminate\Support\Facades\Log;

class EmailValidationService
{
    public function validateEmail($email, $fromEmail)
    {
        // Whitelisted emails bypass validation
        $whitelistedEmails = [
            'info@digitalideasltd.co.uk',
            'aryanchaudhary22222@gmail.com'
        ];

        if (in_array($email, $whitelistedEmails)) {
            return ['status' => true, 'message' => 'Whitelisted email.'];
        }


        $ip = request()->ip();
        $attempts = VerifyEmail::where('ip', $ip)->where('created_on', '>=', now()->subDay())->count();
        if ($attempts > 2) {
            return ['status' => false, 'message' => 'Too many requests from your IP. Please try again later.'];
        }

        // Check rate limit (last 24 hours)
        $attemptCount = VerifyEmail::where('email_to', $email)
            ->where('email_from', $fromEmail)
            ->where('created_on', '>=', now()->subDay())
            ->count();

        if ($attemptCount >= 2) {
            return [
                'status' => false,
                'message' => 'You have exceeded the maximum number of validation attempts. Please try again later.'
            ];
        }

        // Check if email is already verified
        $existingVerification = VerifyEmail::where('email_to', $email)
            ->where('email_from', $fromEmail)
            ->orderBy('created_on', 'desc')
            ->first();

        if ($existingVerification && $existingVerification->to_verified == 1) {
            return ['status' => true, 'message' => 'Email is already verified.'];
        }

        // Proceed with Verifalia API call
        try {
            $verifalia = new VerifaliaRestClient([
                'username' => config('services.verifalia.username') ?? 'info@digitalideasltd.co.uk',
                'password' => config('services.verifalia.password') ?? 'DigitalSystem@#431',
            ]);

            $result = $verifalia->emailValidations->submit($email);
            $isValid = ($result->entries[0]->status === 'Success') ? 1 : 0;

            // Store result in database
            VerifyEmail::create([
                'email_to' => $email,
                'email_from' => $fromEmail,
                'to_verified' => $isValid,
                'status' => 1,
                'ip' => $ip,
                'created_on' => now(),
            ]);

            return [
                'status' => $isValid === 1,
                'message' => $isValid ? 'Email is valid.' : 'Invalid or disposable email address.'
            ];
        } catch (\Exception $e) {
            Log::error('Verifalia API Error', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Email validation failed due to an error. Please try again later.'
            ];
        }
    }
}