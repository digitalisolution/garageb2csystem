<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\AdminRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\EmailValidationService;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Http\Requests\AdminRegisterRequest  $request
     * @param  \App\Services\EmailValidationService  $emailValidationService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(AdminRegisterRequest $request, EmailValidationService $emailValidationService)
    {
        // Validate the email using EmailValidationService
        $validationResult = $emailValidationService->validateEmail(
            $request->input('email'),
            $request->input('email') // Use the app's default sender email address
        );

        if (!$validationResult['status']) {
            return back()->withErrors(['email' => $validationResult['message']])->withInput();
        }

        // Proceed with user creation if email is valid
        $user = $this->create($request->validated());

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}