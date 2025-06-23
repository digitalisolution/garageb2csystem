<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class CustomerResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/'; // Redirect after reset

    public function __construct()
    {
        $this->middleware('guest:customer'); // Use the 'customer' guard
    }

    protected function broker()
    {
        return Password::broker('customers'); // Use the 'customers' broker
    }
    public function showResetForm(Request $request, $token = null)
    {
        \Log::info('Password reset form loaded:', [
            'token' => $token,
            'email' => $request->email,
        ]);

        return view('auth.customer.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
    protected function guard()
    {
        return \Auth::guard('customer'); // Use the 'customer' guard
    }
    protected function credentials(Request $request)
    {
        // dd($request);
        return [
            'customer_email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'token' => $request->input('token'),
        ];
    }
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
    protected function reset(Request $request)
    {
        // dd($request);
        // \Log::info('Attempting password reset:', [
        //     'email' => $request->email,
        //     'token' => $request->token,
        // ]);

        // Validate the request
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Attempt to reset the password
        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);

                \Log::info('Password reset successful:', [
                    'email' => $user->getEmailForPasswordReset(),
                ]);
            }
        );

        // Log the response
        // \Log::info('Password reset response:', ['response' => $response]);

        // Return success or failure response
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }
}