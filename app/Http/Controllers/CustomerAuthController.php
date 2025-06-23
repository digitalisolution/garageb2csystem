<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\RegionCounty;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\MetaSettings;
use App\Http\Requests\LoginFormRequest;
use App\Services\EmailValidationService;
use App\Http\Requests\RegisterFormRequest;



class CustomerAuthController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }
    // Show Registration Form
    public function showRegistrationForm()
    {

        $counties = RegionCounty::where('status', 1)->get(); // Fetch active counties

        return view('customer.register', compact('counties'));
    }

    // public function customerRegister(Request $request)
    // {
    //     $request->validate([
    //         'customer_name' => 'required|string|min:2|max:50',
    //         'customer_last_name' => 'nullable|string|min:2|max:50',
    //         'customer_email' => 'required|string|email|max:255|unique:customers,customer_email',
    //         'customer_contact_number' => 'required|string|min:10|max:15',
    //         'password' => 'required|string|min:8|confirmed',
    //         'company_name' => 'required|string|max:100',
    //         'billing_address_country' => 'required|string',
    //         'billing_address_street' => 'required|string|max:100',
    //         'billing_address_city' => 'required|string|max:50',
    //         'billing_address_postcode' => 'required|string|max:10',
    //         'billing_address_county' => 'required|string',
    //     ]);
    //     $recaptchaSettings = MetaSettings::whereIn('name', ['google_recaptcha_site_key', 'google_recaptcha_secret_key', 'google_recaptcha_status'])
    //     ->pluck('content', 'name');

    // $siteKey = $recaptchaSettings['google_recaptcha_site_key'] ?? null;
    // $secretKey = $recaptchaSettings['google_recaptcha_secret_key'] ?? null;
    // $recaptchaStatus = $recaptchaSettings['google_recaptcha_status'] ?? 'inactive';
    // if ($recaptchaStatus === 'active') {
    //     $rules['g-recaptcha-response'] = 'required';
    // }

    // // Validate input
    // $validated = $request->validate($rules, [
    //     'g-recaptcha-response.required' => 'Please complete the reCAPTCHA to submit the form.'
    // ]);

    // // If reCAPTCHA is active, verify response with Google
    // if ($recaptchaStatus === 'active') {
    //     $recaptchaResponse = $request->input('g-recaptcha-response');

    //     $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
    //         'secret' => $secretKey,
    //         'response' => $recaptchaResponse
    //     ]);

    //     $recaptchaData = $response->json();

    //     if (!$recaptchaData['success']) {
    //         return back()->withErrors(['g-recaptcha-response' => 'Google reCAPTCHA verification failed. Please try again.'])->withInput();
    //     }
    // }
    // $validationResult = $this->emailValidationService->validateEmail($request->email);
    //     if (!$validationResult['status']) {
    //         return back()->withErrors(['email' => $validationResult['message']])->withInput();
    //     }

    //     $validatedData = $request->all();
    //     $validatedData['password'] = Hash::make($validatedData['password']);
    //     $validatedData['remember_token'] = Str::random(60);

    //     $customer = Customer::create($validatedData);
    //     Auth::guard('customer')->login($customer, true);

    //     return redirect()->route('home');
    // }

   

    public function customerRegister(RegisterFormRequest $request)
    {  
        // Validate email using the service
        $validationResult = $this->emailValidationService->validateEmail($request->customer_email,$request->customer_email);
        if (!$validationResult['status']) {
            return back()->withErrors(['customer_email' => $validationResult['message']])->withInput();
        }
    
        // Prepare data for database insertion
        $validatedData = $request->only([
            'customer_name',
            'customer_last_name',
            'customer_email',
            'customer_contact_number',
            'password',
            'company_name',
            'billing_address_country',
            'billing_address_street',
            'billing_address_city',
            'billing_address_postcode',
            'billing_address_county',
        ]);
    
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['remember_token'] = Str::random(60);
    
        // Create customer and log them in
        $customer = Customer::create($validatedData);
        Auth::guard('customer')->login($customer, true);
    
        return redirect()->route('home')->with('success', 'Registration successful!');
    }
    
    // Show Login Form
    public function showLoginForm()
    {
        return view('customer.login');
    }
    public function ShowforgotPassward()
    {
        return view('customer.forgot-password');
    }
    // Handle Login

    public function customerlogin(LoginFormRequest $request)
    {
        $credentials = $request->validated();

        // **Fix: Use only email for rate limiting**
        $rateLimitKey = 'login:' . $request->customer_email;

        // Log::info("Rate Limiter Check for Key: {$rateLimitKey}");
        // Log::info("Attempts before login: " . RateLimiter::attempts($rateLimitKey));

        // **Step 1: Block if too many attempts**
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->withErrors([
                'customer_email' => "Too many login attempts. Try again in " . ceil($seconds / 60) . " minutes."
            ])->withInput();
        }

        // **Step 2: Find customer**
        $customer = Customer::where('customer_email', $credentials['customer_email'])->first();

        if (!$customer) {
            RateLimiter::hit($rateLimitKey, 3600); // Block for 1 hour
            return back()->withErrors(['customer_email' => 'Customer not found.'])->withInput();
        }

        // **Step 3: Validate password**
        if (!Hash::check($credentials['password'], $customer->password)) {
            RateLimiter::hit($rateLimitKey, 3600); // Block for 1 hour
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        // **Step 4: Reset on success**
        RateLimiter::clear($rateLimitKey);
        Auth::guard('customer')->login($customer, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }


    public function forgotPassward(Request $request)
    {
        $credentials = $request->validate([
            'customer_email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.myaccount'));
        }

        return back()->withErrors(['customer_email' => 'Invalid credentials']);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }


}