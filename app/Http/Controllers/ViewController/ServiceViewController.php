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
use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;

class ServiceViewController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }

    public function services()
    {
        // Get services from the database
        $services = CarService::where('status', 1)->get();
        $vehicleData = Session::get('vehicleData', []);
        $counties = RegionCounty::pluck('name', 'zone_id');
        $countries = Countries::pluck('name', 'country_id');
        return view('service.service', compact('services', 'vehicleData', 'counties','countries'));
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

    public function handleEnquiry(BaseFormRequest $request)
    {
            $validated = $request->validate([
                'vehicle_reg' => 'nullable|string|max:8',
                'mileage' => 'nullable|string',
                'first_name' => 'required|string|min:2|max:50|regex:/^[A-Za-z\s]+$/',
                'last_name' => 'nullable|string|min:2|max:50|regex:/^[A-Za-z\s]+$/',
                'email' => 'required|email',
                'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9\-\+\(\)\s]+$/',
                'address' => 'required||string|min:5|max:200',
                'company' => 'nullable|string|max:100',
                'city' => 'required|string|max:100',
                'county' => 'required|string|max:50',
                'country' => 'required|string|max:50',
                'postcode' => 'required|string|max:10|regex:/^[A-Za-z0-9\s]{3,10}$/',
                'message' => 'nullable|string|min:2|max:500|regex:/^[A-Za-z\s]+$/',
                'selected_services' => 'required|array',
            ]);

            $validationResult = $this->emailValidationService->validateEmail($validated['email'],$validated['email']);
            if (!$validationResult['status']) {
                return back()->withErrors(['customer_email' => $validationResult['message']])->withInput();
            }
            
            $countyName = RegionCounty::where('zone_id', $validated['county'])->value('name');
            $countryName = Countries::where('country_id', $validated['country'])->value('name');
            // $customer = Customer::firstOrCreate(
            // ['customer_email' => $validated['email']],
            // [
            //             'customer_name' => $validated['customer_name'],
            //             'customer_last_name' => $validated['last_name'],
            //             'customer_contact_number' => $validated['phone_number'],
            //             'customer_address' => $validated['address'],
            //             'billing_address_city' => $validated['city'],
            //             'billing_address_postcode' => $validated['postcode'],
            //             'billing_address_county' => $validated['county'],
            //             'billing_address_country' => $validated['country'],
            //             'company_name' => $validated['company_name'],
            // ]
            // );

            $estimate = Estimate::create([
                // 'customer_id' => $customer->id,
                'name' => $validated['first_name'].' '.$validated['last_name'] ?? 'Customer',
                'email' => $validated['email'] ?? null,
                'mobile' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'company_name' => $validated['company'] ?? null,
                'city' => $validated['city'] ?? null,
                'county' => $countyName ?? null,
                'country' => $countryName ?? null,
                'zone' => $validated['postcode'] ?? null,
                'notes' => $validated['message'] ?? null,
                'vehicle_reg_number' => $validated['vehicle_reg'] ?? null,
                'status' => 'pending',
                'estimate_origin' => 'Website',
                'estimate_date' => Carbon::now(), // optional
                'fitting_type' => 'fully_fitted', // or based on form
                'grandTotal' => 0,
                'balance_price' => 0,
            ]);

            $services = CarService::whereIn('service_id', $validated['selected_services'])->get();

            foreach ($services as $service) {
                WorkshopService::create([
                    'workshop_id' => $estimate->id,
                    'ref_type' => 'estimate',
                    'service_id' => $service->service_id,
                    'service_name' => $service->name,
                    'tax_class_id' => $service->tax_class_id,
                    'quantity' => 1,
                    'service_price' => $service->price ?? 0,
                    'product_type' => 'service',
                    'fitting_type' => 'fully_fitted',
                ]);
            }

            return back()->with('success', 'Your enquiry has been sent successfully.');
    }


}
