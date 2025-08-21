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
        // Add customer IP
        $ipAddress = $this->getClientIp($request);
        $validatedData['ip_address'] = $ipAddress ?? 'Unknown';

        //$emailData['ip_address'] = $ipAddress ?? 'Unknown';

        // Create customer and log them in
        $customer = Customer::create($validatedData);
        Auth::guard('customer')->login($customer, true);

        try {
            $garage = GarageDetails::first();
            if (!$garage || !$garage->email) {
                return redirect()->back()->with('error', 'Garage details are not configured properly.');
            }
            // Get garage & admin emails from .env or fallback
            $garageEmail = $garage->email;
            $adminEmail = env('ADMIN_NOTIFICATION_EMAIL', 'info@digitalideasltd.co.uk');

            // Prepare data to pass to email templates
            $emailData = [
                'name' => $validatedData['customer_name'],
                'last_name' => $validatedData['customer_last_name'] ?? '',
                'email' => $validatedData['customer_email'],
                'phone' => $validatedData['customer_contact_number'],
                'address_street' => $validatedData['billing_address_street'],
                'address_city' => $validatedData['billing_address_city'],
                'address_postcode' => $validatedData['billing_address_postcode'],
                'address_county' => $validatedData['billing_address_county'],
                'address_country' => $validatedData['billing_address_country'],
                'ip_address' => $validatedData['ip_address'], // 🔹 Include IP in email data
                'registered_at' => now()->format('d/m/Y H:i:s')
            ];

            // Send to customer
            Mail::send('emails.customer_registration_confirmation', $emailData, function ($message) use ($emailData) {
                $message->to($emailData['email'])
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->subject("Welcome to Our Garage Service");
            });

            // Send to garage owner
            Mail::send('emails.garage_new_customer_notification', $emailData, function ($message) use ($emailData, $garageEmail) {
                $message->to($garageEmail)
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->replyTo($emailData['email'], $emailData['name'])
                        ->subject("New Customer Registered: {$emailData['name']} {$emailData['last_name']}");
            });

            // Send to admin
            Mail::send('emails.garage_new_customer_notification', $emailData, function ($message) use ($emailData, $adminEmail) {
                $message->to($adminEmail)
                        ->from(env('MAIL_FROM_ADDRESS'))
                        ->replyTo($emailData['email'], $emailData['name'])
                        ->subject("New Customer Registration (Admin Copy)");
            });

        } catch (\Exception $e) {
            \Log::error("Failed to send registration email(s): " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $emailData
            ]);
        }
    
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

    protected function getClientIp(Request $request)
{
    $headers = [
        'CF-Connecting-IP',    // Cloudflare
        'X-Forwarded-For',     // Proxies/load balancers
        'X-Real-IP',           // Nginx
    ];

    foreach ($headers as $header) {
        if ($request->headers->has($header)) {
            $ips = explode(',', $request->headers->get($header));

            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $ip; // ✅ return first valid IPv4
                }
            }
        }
    }

    // Fallback: check Laravel’s request->ip()
    $ip = $request->ip();
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ip;
    }

    return null; // no IPv4 found
}




}