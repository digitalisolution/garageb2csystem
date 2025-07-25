<?php

namespace App\Http\Controllers\ViewController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\GarageDetails;
use App\Mail\ContactToCustomer;
use App\Mail\ContactToAdmin;
use App\Models\CarService;
use Illuminate\Http\Request;
use App\Rules\NotSpamContent;
use Illuminate\Support\Facades\Mail;
use App\Models\MetaSettings;
use App\Models\Estimate;
use App\Models\Customer;
use App\Models\RegionCounty;
use App\Models\Countries;
use App\Models\WorkshopService;
use App\Services\EmailValidationService;
use App\Packages\OtpEmailVerification\OtpService;
use App\Http\Requests\EnquiryFormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Rules\NoTestCustomer;
use App\Mail\EnquiryToCustomer;

class ServiceViewController extends Controller
{
    protected $emailValidationService;
    protected $otpService;

    public function __construct(EmailValidationService $emailValidationService,OtpService $otpService)
    {
        $this->emailValidationService = $emailValidationService;
        $this->otpService = $otpService;
    }

    public function services()
    {
        // Get services from the database
        $services = CarService::where('status', 1)->get();
        $vehicleData = Session::get('vehicleData', []);
        $counties = RegionCounty::pluck('name', 'zone_id');
        $countries = Countries::pluck('name', 'country_id');
        return view('service.service', compact('services', 'vehicleData', 'counties', 'countries'));
    }

    public function show($slug)
    {
        // Get the service by its slug
        $service = CarService::where('slug', $slug)->first();
        if (!$service) {
            abort(404); // Return 404 if service not found
        }

        return view('service.serviceDetails', compact('service'));
    }

    public function submitContactForm(Request $request)
    {

        // Fetch Google reCAPTCHA settings from the database
        $recaptchaSettings = MetaSettings::whereIn('name', ['google_recaptcha_site_key', 'google_recaptcha_secret_key', 'google_recaptcha_status'])
            ->pluck('content', 'name');

        $siteKey = $recaptchaSettings['google_recaptcha_site_key'] ?? null;
        $secretKey = $recaptchaSettings['google_recaptcha_secret_key'] ?? null;
        $recaptchaStatus = $recaptchaSettings['google_recaptcha_status'] ?? 'inactive';

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'otp_code' => 'required|string|max:6',
            'subject' => ['required', new NotSpamContent()],
            'message' => ['required', new NotSpamContent()],
        ];

        if ($recaptchaStatus === 'active') {
            $rules['g-recaptcha-response'] = 'required';
        }

        // Validate input
        $validated = $request->validate($rules, [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA to submit the form.'
        ]);

        // If reCAPTCHA is active, verify response with Google
        if ($recaptchaStatus === 'active') {
            $recaptchaResponse = $request->input('g-recaptcha-response');

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse
            ]);

            $recaptchaData = $response->json();

            if (!$recaptchaData['success']) {
                return back()->withErrors(['g-recaptcha-response' => 'Google reCAPTCHA verification failed. Please try again.'])->withInput();
            }
        }

        if (!$this->otpService->verify($request->email, $request->otp_code)) {
        return back()->withErrors(['otp_code' => 'Invalid or expired OTP'])->withInput();
        }

        // Retrieve garage details
        $garage = GarageDetails::first();
        if (!$garage || !$garage->email) {
            return redirect()->back()->with('error', 'Garage details are not configured properly.');
        }
        $fromEmail = $garage->email ?? 'info@digitalideasltd.co.uk';

        $validationResult = $this->emailValidationService->validateEmail($request->email, $fromEmail);
        if (!$validationResult['status']) {
            return back()->withErrors(['email' => $validationResult['message']])->withInput();
        }
        $ip = request()->ip();

        // Build email data
        $emailData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'ip' => $ip,
            'user_message' => $validated['message'],
        ];

        Mail::to('info@digitalideasltd.co.uk')->send(new ContactToAdmin($emailData, $garage));
        Mail::to($garage->email)->send(new ContactToAdmin($emailData, $garage));

        // Send confirmation to customer
        Mail::to($validated['email'])->send(new ContactToCustomer($emailData, $garage));

        return redirect()->back()->with('success', 'Your inquiry has been sent successfully!');
    }

    public function handleEnquiry(EnquiryFormRequest $request)
    {
            $validated = $request->validated();
            $validationResult = $this->emailValidationService->validateEmail($validated['email'], $validated['email']);
            if (!$validationResult['status']) {
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => [$validationResult['message']]],
                    'message' => 'Email validation failed.'
                ], 422);
            }

            // Prevent duplicate entries in last 24 hours
            $duplicateCheck = Estimate::where('email', $validated['email'])
                ->where('vehicle_reg_number', $validated['vehicle_reg'])
                ->where('created_at', '>=', now()->subHours(24))
                ->where('estimate_origin', 'Website')
                ->exists();

            if ($duplicateCheck) {
                return response()->json([
                    'success' => false,
                    'errors' => ['email' => ['You have already submitted an enquiry for this vehicle in the last 24 hours.']],
                    'message' => 'Duplicate enquiry detected.'
                ], 422);
            }

            // Start DB transaction
            DB::beginTransaction();

            try {

                $countyName = RegionCounty::where('zone_id', $validated['county'])->value('name');
                $countryName = Countries::where('country_id', $validated['country'])->value('name');

                if (!$countyName || !$countryName) {
                    throw new \Exception('Invalid county or country selected.');
                }

                // Check if customer exists
                $customer = Customer::where('customer_email', strtolower(trim($validated['email'])))->first();

                if (!$customer) {
                    // Create new customer
                    $customer = Customer::create([
                        'customer_name' => trim($validated['first_name']),
                        'customer_last_name' => trim($validated['last_name'] ?? ''),
                        'customer_email' => strtolower(trim($validated['email'])),
                        'customer_contact_number' => $validated['phone'] ? preg_replace('/\D/', '', $validated['phone']) : null,
                        'customer_address' => trim($validated['address']),
                        'customer_city' => trim($validated['city']),
                        'customer_county' => $countyName,
                        'customer_country' => $countryName,
                        'customer_postcode' => strtoupper(trim($validated['postcode'])),
                        'customer_origin' => 'Website',
                        'customer_status' => 'active',
                    ]);
                }

                // Create estimate
                $estimate = Estimate::create([
                    'customer_id' => $customer->id,
                    'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                    'email' => strtolower(trim($validated['email'])),
                    'mobile' => $validated['phone'] ? preg_replace('/\D/', '', $validated['phone']) : null,
                    'address' => trim($validated['address']),
                    'city' => trim($validated['city']),
                    'county' => $countyName,
                    'country' => $countryName,
                    'zone' => strtoupper(trim($validated['postcode'])),
                    'notes' => $validated['message'] ?? null,
                    'vehicle_reg_number' => $validated['vehicle_reg'] ? strtoupper(trim($validated['vehicle_reg'])) : null,
                    'mileage' => $validated['mileage'] ? trim($validated['mileage']) : null,
                    'status' => 'pending',
                    'estimate_origin' => 'Website',
                    'estimate_date' => now(),
                    'fitting_type' => 'fully_fitted',
                    'grandTotal' => 0,
                    'balance_price' => 0,
                ]);

                // Validate and get services
                $services = CarService::whereIn('service_id', $validated['selected_services'])->get();

                if ($services->count() !== count($validated['selected_services'])) {
                    throw new \Exception('Some selected services are invalid.');
                }

                $totalAmount = 0;

                // Attach services to estimate
                foreach ($services as $service) {
                    $servicePrice = floatval($service->price ?? 0);
                    $totalAmount += $servicePrice;

                    WorkshopService::create([
                        'workshop_id' => $estimate->id,
                        'ref_type' => 'estimate',
                        'service_id' => $service->service_id,
                        'service_name' => $service->name,
                        'tax_class_id' => $service->tax_class_id,
                        'quantity' => 1,
                        'service_price' => $servicePrice,
                        'product_type' => 'service',
                        'fitting_type' => 'fully_fitted',
                    ]);
                }

                // Update estimate with total amount
                $estimate->update([
                    'grandTotal' => $totalAmount,
                    'balance_price' => $totalAmount,
                ]);

                // Commit transaction
                DB::commit();
                
                if ($validated['email']) {
                $this->sendEnquiryConfirmationEmail($validated, $estimate->id);
                }

                // Return success JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Your enquiry has been submitted successfully! We will contact you soon.',
                    'estimate_id' => $estimate->id,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'There was an error saving your enquiry.',
                    'error_details' => $e->getMessage()
                ], 500);
            }
    }

        protected function sendEnquiryConfirmationEmail(array $validated, $orderId)
    {
        $nameRule = new NoTestCustomer();
        $emailRule = new NoTestCustomer();

        if (
            !$nameRule->passes('customer_name', trim($validated['first_name'])) ||
            !$emailRule->passes('email', strtolower(trim($validated['email'])))
        ) {
            Log::info('Skipping email due to spam-like customer details.', [
                'customer_name' => $validated['first_name'],
                'email' => $validated['email'],
            ]);
            return true;
        }

        try {
            $customer = [
                'customer_name' =>trim($validated['first_name']),
                'email' => strtolower(trim($validated['email'])),
            ];

            $garage = GarageDetails::first();
            $garageEmail = $garage?->email;

            // Send to customer
            Mail::to($customer['email'])->send(new EnquiryToCustomer($orderId, $customer, $garage));

            // Send to owner
            $ownerEmail = 'info@digitalideasltd.co.uk';
            Mail::to($ownerEmail)->send(new EnquiryToCustomer($orderId, $customer, $garage));

            // Send to garage
            if ($garageEmail) {
                Mail::to($garageEmail)->send(new EnquiryToCustomer($orderId, $customer, $garage));
            } else {
                Log::warning('Garage email not found.', ['email' => $garageEmail]);
            }

        } catch (\Exception $e) {
            Log::error('Error during email submission.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

}

