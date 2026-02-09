<?php
namespace App\Http\Controllers;

use App\Packages\OtpEmailVerification\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpVerificationController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns'
        ]);

        $response = $this->otpService->send($request->email);

        return response()->json([
            'message' => $response['message']
        ], $response['success'] ? 200 : 422);
    }


    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'otp' => 'required|numeric'
        ]);

        if (!Cache::has('otp_' . md5($request->email))) {
            return response()->json(['status' => 'expired'], 410);
        }

        if ($this->otpService->verify($request->email, $request->otp)) {
            return response()->json(['status' => 'verified']);
        }

        return response()->json(['status' => 'invalid'], 422);

    }
}