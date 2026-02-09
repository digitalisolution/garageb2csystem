<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ResetFormRequest;
use Illuminate\Support\Facades\Log;


class CustomerForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest:customer'); // Use the 'customer' guard
    }

    protected function broker()
    {
        return Password::broker('customers'); // Use the 'customers' broker
    }
    public function showLinkRequestForm()
    {
        return view('auth.customer.passwords.email'); // Use the customer-specific view
    }

    public function sendResetLinkEmail(ResetFormRequest $request)
    {
        $email = $request->validated('email');

        // Query the database for the customer
        $customer = DB::table('customers')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower($email)])
            ->whereNull('deleted_at') // Exclude soft-deleted records
            ->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        // Trigger the password reset process
        $response = Password::broker('customers')->sendResetLink(
            ['customer_email' => $email] // Use 'email' as expected by the broker
        );

        // Handle the response
        if ($response === Password::RESET_LINK_SENT) {
            return back()->with('status', 'We have sent you a password reset link!');
        } else {
            return back()->withErrors(['email' => 'Failed to send reset link.']);
        }
    }

}
