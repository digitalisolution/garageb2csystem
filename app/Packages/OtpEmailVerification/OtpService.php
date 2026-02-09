<?php
namespace App\Packages\OtpEmailVerification;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailValidationService;
use App\Models\GarageDetails;
use App\Mail\OtpMail;


class OtpService
{
    protected $ttl = 300; // 10 minutes in seconds
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }

    public function generate(string $email): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $otp = '';
        for ($i = 0; $i < 6; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        Cache::put($this->cacheKey($email), encrypt($otp), $this->ttl);
        return $otp;
    }

    public function send(string $email): array
    {
        $garage = GarageDetails::first();
        if (!$garage || !$garage->email) {
            return redirect()->back()->with('error', 'Garage details are not configured properly.');
        }
        $fromEmail = $garage->email ?? 'info@digitalideasltd.co.uk';

        $validation = $this->emailValidationService->validateEmail($email, $fromEmail);
        
        if (!$validation['status']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $otp = $this->generate($email);

        try {
            Mail::to($email)->send(new OtpMail($otp));

            return [
                'success' => true,
                'message' => 'OTP sent successfully.'
            ];

        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP email. Please try again later.'
            ];
        }
    }

    public function verify(string $email, string $otp): bool
    {
        $cached = Cache::get($this->cacheKey($email));
        $cached = $cached ? decrypt($cached) : null;

        if ($cached && (int) $cached === (int) $otp) {
            Cache::forget($this->cacheKey($email));

            return true;
        }

        return false;
    }



    protected function cacheKey(string $email): string
    {
        return 'otp_' . md5($email);
    }
}