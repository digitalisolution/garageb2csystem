<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\AdminLoginRequest;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        // Use the custom form request for validation
        $loginRequest = app(AdminLoginRequest::class);
        $loginRequest->setContainer(app())->validateResolved();
    }

    protected function attemptLogin(\Illuminate\Http\Request $request)
    {
        $credentials = $request->only($this->username(), 'password');

        // Attempt to log the user in
        return $this->guard()->attempt(
            $credentials,
            $request->filled('remember')
        );
    }
}