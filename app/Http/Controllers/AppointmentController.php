<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\GarageDetails;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailValidationService;

class AppointmentController extends Controller
{
    protected $emailValidationService;

    public function __construct(EmailValidationService $emailValidationService)
    {
        $this->emailValidationService = $emailValidationService;
    }

    public function create()
    {
        return view('appointment.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'first_name'     => 'required|string|max:100',
        'last_name'      => 'required|string|max:100',
        'email'          => 'required|email',
        'phone'          => 'nullable|string|max:20',
        'vehicle_type'   => 'required|string',
        'vehicle_make'   => 'nullable|string',
        'vehicle_model'  => 'nullable|string',
        'vehicle_year'   => 'nullable|string',
        'tyresize'       => 'nullable|string|max:50',
        'message'        => 'nullable|string',
        'choose_date'    => 'required|date',
        'choose_time'    => 'required|string',
    ]);

         $garage = GarageDetails::first();

    $validationResult = $this->emailValidationService->validateEmail($validated['email'], $garage->email);
    if (!$validationResult['status']) {
        return back()->withErrors(['email' => $validationResult['message']])->withInput();
    }

    $appointment = Appointment::create($validated);

    Mail::to($validated['email'])->send(new AppointmentConfirmation($validated, $garage));
    Mail::to($garage->email)->send(new AppointmentNotification($validated,$garage));
    Mail::to('info@digitalideasltd.co.uk')->send(new AppointmentNotification($validated, $garage));

        if (!$this->emailValidationService->validateEmail($validated['email'], $garage->email)) {
            return back()->withErrors(['email' => 'You have reached submiting form limit for today. Please try again later.'])->withInput();
        }

    return redirect()->back()->with('success', 'Appointment submitted successfully!');
}
}
