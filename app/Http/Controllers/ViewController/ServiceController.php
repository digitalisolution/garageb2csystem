<?php

namespace App\Http\Controllers\ViewController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\GarageDetails;
use App\Mail\ContactToCustomer;
use App\Mail\ContactToAdmin;
use App\Models\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\MetaSettings;
use Verifalia\VerifaliaRestClient;
use App\Services\EmailValidationService;

class ServiceController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }

    public function services()
    {
        // Get services from the database
        $services = \DB::table('car_services')->where('status', 1)->get();
        $vehicleData = Session::get('vehicleData', []);
        return view('service', compact('services', 'vehicleData'));
    }

    public function show($slug)
    {
        // Get the service by its slug
        $service = \DB::table('car_services')->where('slug', $slug)->first();
        if (!$service) {
            abort(404); // Return 404 if service not found
        }

        return view('serviceDetails', compact('service'));
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
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
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

        $validationResult = $this->emailValidationService->validateEmail($request->email, $garage->email);
        if (!$validationResult['status']) {
            return back()->withErrors(['email' => $validationResult['message']])->withInput();
        }


        // Build email data
        $emailData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'user_message' => $validated['message'],
        ];

    Mail::to('info@digitalideasltd.co.uk')->send(new ContactToAdmin($emailData, $garage));
    Mail::to($garage->email)->send(new ContactToAdmin($emailData, $garage));

    // Send confirmation to customer
    Mail::to($validated['email'])->send(new ContactToCustomer($emailData, $garage));

        // // Send emails
        // Mail::send('emails.contact_form', $emailData, function ($msg) use ($validated, $garage) {
        //     $msg->to('info@digitalideasltd.co.uk')
        //         ->from(config('mail.from.address'), $garage->garage_name)
        //         ->replyTo($validated['email'], $validated['name'])
        //         ->subject($validated['subject']);
        // });

        // Mail::send('emails.contact_form', $emailData, function ($msg) use ($validated, $garage) {
        //     $msg->to($garage->email)
        //         ->from(config('mail.from.address'), $garage->garage_name)
        //         ->replyTo($validated['email'], $validated['name'])
        //         ->subject($validated['subject']);
        // });

        // Mail::send('emails.customer_confirmation', $emailData, function ($msg) use ($validated, $garage) {
        //     $msg->to($validated['email'])
        //         ->from(config('mail.from.address'), $garage->garage_name)
        //         ->replyTo($garage->email, $garage->garage_name ?? 'Garage')
        //         ->subject('Thank you for contacting us!');
        // });
        if (!$this->emailValidationService->validateEmail($validated['email'], $garage->email)) {
            return back()->withErrors(['email' => 'You have reached submiting form limit for today. Please try again later.'])->withInput();
        }

        return redirect()->back()->with('success', 'Your inquiry has been sent successfully!');
    }
}
