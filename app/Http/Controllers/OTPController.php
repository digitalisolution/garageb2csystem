<?php
// app/Http/Controllers/OTPController.php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\EmailOtp;

class OTPController extends Controller
{
   public function sendOtp(Request $request)
{
    $request->validate(['email' => 'required|email']);
    
    $code = rand(100000, 999999);
    
    Otp::updateOrCreate(
        ['email' => $request->email],
        ['code' => $code, 'expires_at' => now()->addMinutes(10)]
    );

    // Mail logic here...
    Mail::to($request->email)->send(new SendOtpCode($code));

    return response()->json(['message' => 'OTP sent to your email.']);
}


    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpRecord = EmailOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            session(['otp_verified_email' => $request->email]);
            return response()->json(['status' => 'verified']);
        }

        return response()->json(['error' => 'Invalid OTP'], 422);
    }
}

