<?php

namespace App\Http\Controllers;

use App\Mail\CustomerRegistrationConfirmation;
use App\Mail\NewCustomerRegisteredToGarage;
use App\Models\Garage;
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
use App\Http\Requests\GarageLoginFormRequest;
use App\Services\EmailValidationService;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\GarageRegisterFormRequest;



class GarageAuthController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }
    // Show Registration Form
    public function showRegistrationForm()
    {

        $counties = RegionCounty::where('status', 1)->get();
        return view('garage.register', compact('counties'));
    }

public function garageRegister(GarageRegisterFormRequest $request)
{
    try {
        $validationResult = $this->emailValidationService->validateEmail($request->garage_email, $request->garage_email);
        if (!$validationResult['status']) {
            return back()->withErrors(['garage_email' => $validationResult['message']])->withInput();
        }
        $logoFilename = $this->handleLogoUpload($request, $request->garage_name);

        // Prepare Data
        $validatedData = [
            'garage_name' => $request->garage_name,
            'garage_last_name' => $request->garage_last_name ?? '',
            'garage_email' => $request->garage_email,
            'garage_contact_number' => $request->garage_phone ?? $request->garage_contact_number,
            'garage_phone' => $request->garage_phone,
            'garage_mobile' => $request->garage_mobile,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name ?? $request->garage_name,
            'garage_street' => $request->garage_street,
            'garage_city' => $request->garage_city,
            'garage_zone' => $request->garage_zone ?? '',
            'garage_county' => $request->garage_zone ?? $request->garage_county,
            'garage_country' => $request->garage_country ?? 'United Kingdom',
            'remember_token' => Str::random(60),
            'ip_address' => IpHelper::getClientIp($request),
            'garage_website_url' => $request->garage_website_url,
            'garage_garage_opening_time' => $request->garage_opening_time,
            'garage_logo' => $logoFilename,
            'garage_order_types' => $request->has('garage_order_types') 
                ? implode(',', (array) $request->garage_order_types) 
                : '',
            'garage_status' => 0,
        ];

        $garageModel = Garage::create($validatedData);

        return redirect()->route('garage.register')->with('success', 'Registration successful!');
    } catch (\Exception $e) {
        Log::error("Garage registration failed: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        return back()->withErrors(['general' => 'Registration failed. Please try again.'])->withInput();
    }
}
   protected function handleLogoUpload(Request $request, ?string $garageName = null): ?string
{
    if (!$request->hasFile('garage_logo')) {
        return null;
    }

    $logo = $request->file('garage_logo');
    if (!$logo->isValid()) {
        return null;
    }

    $prefix = $garageName ? Str::slug($garageName, '-') : 'garage';
    $extension = $logo->getClientOriginalExtension();
    $logoName = $prefix . '-logo.' . $extension;

    $domain = str_replace(['http://', 'https://'], '', request()->getHost());
    $basePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/garage_logo/';
    $destinationPath = public_path($basePath);

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0755, true);
    }

    $logo->move($destinationPath, $logoName);

    return $logoName;
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

    public function showLoginForm()
    {
        return view('garage.login');
    }
    public function ShowforgotPassward()
    {
        return view('garage.forgot-password');
    }
    public function garagelogin(GarageLoginFormRequest $request)
    {
        $credentials = $request->validated();
        $rateLimitKey = 'login:' . $request->garage_email;
        $attempts = RateLimiter::attempts($rateLimitKey);
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            \Log::warning("Rate limit exceeded for: {$request->garage_email}", [
                'attempts' => $attempts,
                'available_in' => $seconds
            ]);
            
            return back()->withErrors([
                'garage_email' => "Too many login attempts. Try again in " . ceil($seconds / 60) . " minutes."
            ])->withInput();
        }

        $garage = Garage::where('garage_email', $credentials['garage_email'])->first();
        
        if (!$garage) {
            \Log::warning('Garage not found in database', ['email' => $credentials['garage_email']]);
            RateLimiter::hit($rateLimitKey, 3600);
            return back()->withErrors(['garage_email' => 'Garage not found.'])->withInput();
        }

        // **Step 3: Validate password**
        $passwordCheck = Hash::check($credentials['password'], $garage->password);
        
        if (!$passwordCheck) {
            RateLimiter::hit($rateLimitKey, 3600);
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        if (isset($garage->garage_status) && $garage->garage_status != 1) {
            return back()->withErrors(['garage_email' => 'Account is not active.'])->withInput();
        }
        RateLimiter::clear($rateLimitKey);
        
        Auth::guard('garage')->login($garage, $request->filled('remember'));
        $request->session()->regenerate();
        return redirect()->intended(route('garage.myaccount'));
    }
    public function forgotPassward(Request $request)
    {
        $credentials = $request->validate([
            'garage_email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('garage')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('garage.myaccount'));
        }

        return back()->withErrors(['garage_email' => 'Invalid credentials']);
    }
    public function logout(Request $request)
    {
        Auth::guard('garage')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }


}