<?php

namespace App\Http\Controllers;

use App\Mail\CustomerRegistrationConfirmation;
use App\Mail\NewCustomerRegisteredToGarage;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\RegionCounty;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\MetaSettings;
use App\Helpers\IpHelper;
use App\Models\GarageDetails;
use App\Http\Requests\LoginFormRequest;
use App\Services\EmailValidationService;
use Illuminate\Support\Facades\Mail;
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


   public function customerRegister(RegisterFormRequest $request)
{
    // Validate email using the service
    $validationResult = $this->emailValidationService->validateEmail($request->customer_email, $request->customer_email);
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
    // Add customer IP
    $ipAddress = IpHelper::getClientIp($request);
    $validatedData['ip_address'] = $ipAddress;

    // Create customer and log them in
    $customerModel = Customer::create($validatedData);
    Auth::guard('customer')->login($customerModel, true);

    try {
        $garage = GarageDetails::first();
        if (!$garage || !$garage->email) {
            return redirect()->back()->with('error', 'Garage details are not configured properly.');
        }

        // Prepare data to pass to email templates
        $customerData = [
            'name' => $validatedData['customer_name'],
            'last_name' => $validatedData['customer_last_name'] ?? '',
            'email' => $validatedData['customer_email'],
            'phone' => $validatedData['customer_contact_number'],
            'company_name' => $validatedData['company_name'],
            'address_street' => $validatedData['billing_address_street'],
            'address_city' => $validatedData['billing_address_city'],
            'address_postcode' => $validatedData['billing_address_postcode'],
            'address_county' => $validatedData['billing_address_county'],
            'address_country' => $validatedData['billing_address_country'],
            'ip_address' => $validatedData['ip_address'],
            'registered_at' => now()->format('d/m/Y H:i:s')
        ];

        // Send email to customer
        Mail::to($validatedData['customer_email'])->send(new CustomerRegistrationConfirmation($customerData, $garage));

        // Send email to admin/owner
        $ownerEmail = env('ADMIN_NOTIFICATION_EMAIL', 'info@digitalideasltd.co.uk');
        Mail::to($ownerEmail)->send(new NewCustomerRegisteredToGarage($customerData, $garage));

        // Send email to garage (if different from owner)
        $garageEmail = $garage->email;
        if ($garageEmail && $garageEmail !== $ownerEmail) {
            Mail::to($garageEmail)->send(new NewCustomerRegisteredToGarage($customerData, $garage));
        }

    } catch (\Exception $e) {
        \Log::error("Failed to send registration email(s): " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'data' => $customerData ?? []
        ]);
    }

    return redirect()->route('home')->with('success', 'Registration successful!');
}

protected function getClientIp(Request $request)
{
    // Cloudflare adds the real client IP here
    if ($request->headers->has('CF-Connecting-IP')) {
        $ip = $request->headers->get('CF-Connecting-IP');
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        }
    }
 
    // If multiple proxies, X-Forwarded-For may contain multiple IPs
    if ($request->headers->has('X-Forwarded-For')) {
        $ips = explode(',', $request->headers->get('X-Forwarded-For'));
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        }
    }
 
    // Fallback: use default Laravel detection
    $ip = $request->ip();
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ip;
    }
 
    return null; // no IPv4 found
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