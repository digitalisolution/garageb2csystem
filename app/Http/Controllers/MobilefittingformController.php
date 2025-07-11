<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GarageDetails;
use App\Mail\MobilefittingformConfirmation;
use App\Mail\MobilefittingformNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailValidationService;

class MobilefittingformController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }

    public function create(Request $request)
    {
        $postcode = $request->query('postcode', '');

        return view('mobilefittingform.form', compact('postcode'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name'     => 'required|string|max:100',
        'email'          => 'required|email',
        'phone'          => 'nullable|string|max:20',
        'vehicle_type'   => 'required|string',
        'postcode'       => 'nullable|string',
        'tyresize'       => 'nullable|string|max:50',
        'message'        => 'nullable|string',
    ]);

    $garage = GarageDetails::first();

    $validationResult = $this->emailValidationService->validateEmail($validated['email'], $garage->email);
    if (!$validationResult['status']) {
        return back()->withErrors(['email' => $validationResult['message']])->withInput();
    }    


    // Send emails
    Mail::to($validated['email'])->send(new MobilefittingformConfirmation($validated, $garage));
    Mail::to($garage->email)->send(new MobilefittingformNotification($validated, $garage));
    Mail::to('info@digitalideasltd.co.uk')->send(new MobilefittingformNotification($validated, $garage));

    return redirect()->back()->with('success', 'Your enquiry has been submitted successfully!');
}

}
