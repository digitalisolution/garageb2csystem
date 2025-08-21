<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\Customer;
use App\Models\PaymentRecord;
use App\Services\PaymentAssistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentAssistController extends Controller
{
    protected PaymentAssistService $paymentAssistService;

    public function __construct(PaymentAssistService $paymentAssistService)
    {
        $this->paymentAssistService = $paymentAssistService;
    }


    /**
     * Display the initial payment page for website-initiated payments (similar to make_payment_website).
     * This is the page where the user confirms they want to pay via PaymentAssist for a Job/Workshop.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function showPaymentPageWebsite(Request $request)
    {
        $workshopId = $request->get('jobid');
        // $hash = $request->get('hash'); // Not used in CI example, but you might have one
        $total = $request->get('total');

        // Validate job ID and potentially hash if you use one
        // Implement your own logic to check job validity and restrictions
        $workshop = Workshop::findOrFail($workshopId); // Adjust model/retrieval logic

        // if (!$workshop || $workshop->hash !== $hash) { // Example validation if you had a hash
        //     abort(404, 'Job not found or invalid hash.');
        // }

        // Get customer details (similar to CI)
        $customer = $workshop->customer;
        $billingEmail = '';
        if (auth()->check() && auth()->user()->customer) { // If customer is logged in
            $billingEmail = auth()->user()->customer->email ?? ''; // Adjust based on your auth setup
        } else {
            // Get primary contact email if not logged in (adjust logic as needed)
            // $contact = $this->clients_model->get_contact(get_primary_contact_user_id($workshop->clientid));
            // if ($contact) { $billingEmail = $contact->email; }
            // For now, we'll leave it empty or get it from the job/customer model if available directly
            $billingEmail = $customer->email ?? $workshop->email ?? ''; // Try different sources
        }

        $data = [
            'job' => $workshop,
            'total' => $total,
            'billing_email' => $billingEmail,
            // Add flags like address_2_required etc. if needed by your view
            'address_2_required' => false,
            'state_required' => false,
            'zip_code_required' => false,
        ];

        // Return the specific view for website payments
        return view('gateways.paymentassist.payment_page_website', $data); // Create this view
    }

    /**
     * Handle the initial submission from the website payment page (similar to complete_purchase_website).
     * Checks pre-approval and redirects to PaymentAssist.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiatePaymentWebsite(Request $request)
    {
        $request->validate([
            'jobid' => 'required|integer|exists:workshops,id', // Adjust table/column names
            'total' => 'required|numeric|min:0.01',
        ]);

        $workshopId = $request->input('jobid');
        $total = $request->input('total');

        // Get job and customer details (similar to CI)
        $workshop = Workshop::findOrFail($workshopId); // Adjust model/retrieval
        $customer = $workshop->customer; // Assuming relationship exists

        if (!$customer) {
             Session::flash('error', 'Customer details not found.');
             // Redirect back to the payment page or a relevant error page
             return redirect()->route('paymentassist.pay.website', ['jobid' => $workshopId, 'total' => $total]);
            // Or redirect to a general checkout error page
            // return redirect()->route('checkout.error');
        }

        // Prepare customer data for pre-approval check (similar to CI $inv_data)
        $customerData = [
            'firstname' => $customer->first_name ?? $customer->name ?? '', // Adjust attribute names
            'lastname' => $customer->last_name ?? '',
            'email' => $customer->email ?? '', // Adjust attribute
            'address' => trim(implode(' ', [
                $workshop->billing_street ?? '', // Adjust attribute names
                $workshop->billing_city ?? '',
                $workshop->billing_state ?? '',
                $workshop->billing_zip ?? '',
                // Add country name if needed and available: get_country($workshop->billing_country) ?? ''
            ])),
            'postcode' => $workshop->billing_zip ?? $workshop->postcode ?? '', // Adjust attribute
        ];

        // 1. Check Pre-approval (similar to CI)
        $preApprovalResponse = $this->paymentAssistService->checkPreApproval($customerData);

        // Log the pre-approval attempt
        Log::info("PaymentAssist Pre-approval check for Website Job ID: {$workshopId}", ['response' => $preApprovalResponse]);

        if (!$preApprovalResponse) {
            Session::flash('error', 'Failed to check pre-approval with Payment Assist. Please try again.');
            // Redirect back to payment page
            return redirect()->route('paymentassist.pay.website', ['jobid' => $workshopId, 'total' => $total]);
        }

        // Check if pre-approval was successful (similar to CI logic)
        $isApproved = false;
        $errorMessage = 'Pre-approval check failed.';

        if (isset($preApprovalResponse['status'])) {
            if ($preApprovalResponse['status'] === 'ok' &&
                isset($preApprovalResponse['data']['approved']) &&
                $preApprovalResponse['data']['approved'] == 1) {
                $isApproved = true;
            } elseif ($preApprovalResponse['status'] === 'error') {
                $errorMessage = $preApprovalResponse['msg'] ?? $errorMessage;
                if (isset($preApprovalResponse['data']) && is_array($preApprovalResponse['data'])) {
                    foreach ($preApprovalResponse['data'] as $key => $val) {
                        $errorMessage .= ', ' . strtolower(str_replace("_", " ", $this->getKeyValue($key))) . ' ' . strtolower($val);
                    }
                }
                $errorMessage .= ', Cannot proceed.';
            }
            // Handle other potential 'status' values if needed
        }

        if ($isApproved) {
            // 2. Pre-approved, proceed to begin payment (similar to CI)

            // Prepare payment data for beginPayment (similar to CI $params in begin_website)
            // Use APP_BASE_URL equivalent or define specific routes
            $callbackRoute = route('paymentassist.callback.website', ['jobid' => $workshopId]);
            $orderId = $workshopId . '-' . time(); // Or Str::uuid() for uniqueness

            $paymentData = [
                'order_id' => $orderId,
                'amount' => $total, // Service expects amount, it converts to pence
                'firstname' => $customerData['firstname'],
                'lastname' => $customerData['lastname'],
                'email' => $customerData['email'],
                'address' => $customerData['address'],
                'postcode' => $customerData['postcode'],
                'success_url' => $callbackRoute, // Same URL often handles both
                'failure_url' => $callbackRoute,
                 // Add any other specific fields required by PaymentAssist /begin if needed
            ];

            // 3. Begin Payment (similar to CI)
            $beginResponse = $this->paymentAssistService->beginPayment($paymentData);

            // Log the begin payment attempt
            Log::info("PaymentAssist Begin Payment for Website Job ID: {$workshopId}", ['response' => $beginResponse]);

            if (!$beginResponse) {
                 Session::flash('error', 'Failed to initiate payment with Payment Assist. Please try again.');
                return redirect()->route('paymentassist.pay.website', ['jobid' => $workshopId, 'total' => $total]);
            }

            // Check if /begin was successful (similar to CI)
            if (isset($beginResponse['status']) && $beginResponse['status'] === 'ok' &&
                isset($beginResponse['data']['url'])) {

                // 4. Redirect to Payment Assist (similar to CI)
                $paymentUrl = $beginResponse['data']['url'];
                // Store the order ID in session if needed for later verification
                Session::put('paymentassist_website_order_id', $orderId);
                // Store job ID in session if needed
                Session::put('paymentassist_website_job_id', $workshopId);

                return Redirect::away($paymentUrl); // Redirect to PaymentAssist

            } else {
                // Handle error from /begin (similar to CI)
                $errorMessage = $beginResponse['msg'] ?? 'Unknown error initiating payment.';
                if (isset($beginResponse['data']) && is_array($beginResponse['data'])) {
                    foreach ($beginResponse['data'] as $key => $val) {
                        $errorMessage .= ', ' . strtolower(str_replace("_", " ", $this->getKeyValue($key))) . ' ' . strtolower($val);
                    }
                }
                $errorMessage .= ', Cannot proceed.';
                Session::flash('error', $errorMessage);
                // Redirect back to payment page or error page
                return redirect()->route('paymentassist.pay.website', ['jobid' => $workshopId, 'total' => $total]);
                // Or redirect to a specific checkout re-payment page like CI
                // return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
            }

        } else {
            // Pre-approval failed (similar to CI else block)
            Session::flash('error', $errorMessage);
            // Redirect back to payment page or error page
            return redirect()->route('paymentassist.pay.website', ['jobid' => $workshopId, 'total' => $total]);
             // Or redirect to a specific checkout re-payment page like CI
            // return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
        }
    }

    /**
     * Handle the callback from PaymentAssist for website-initiated payments (similar to callback_website).
     *
     * @param Request $request
     * @param int $workshopId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleCallbackWebsite(Request $request, int $workshopId)
    {
        $token = $request->get('token');

        if (!$token) {
             Log::warning("PaymentAssist website callback received without token for Job ID: {$workshopId}");
             Session::flash('error', 'Invalid callback from Payment Assist.');
             // Redirect to a relevant page (e.g., checkout home, job view, or error page)
             // Example: return redirect()->route('checkout.home');
             // For now, redirecting to home
            return redirect('/');
        }

        // Get job (similar to CI)
        $workshop = Workshop::findOrFail($workshopId); // Adjust model/retrieval

        // 1. Check Payment Status (similar to CI)
        $statusResponse = $this->paymentAssistService->checkStatus($token);

        // Log the status check
        Log::info("PaymentAssist Status check for Website Job ID: {$workshopId} with token: {$token}", ['response' => $statusResponse]);

        if (!$statusResponse) {
             Session::flash('error', 'Failed to verify payment status with Payment Assist.');
             // Redirect to error or relevant page
             // Example: return redirect()->route('checkout.payment.error');
            return redirect('/');
        }

        // Process status response (similar to CI)
        if (isset($statusResponse['status']) && $statusResponse['status'] === 'ok') {

            $paymentStatus = $statusResponse['data']['status'] ?? null;
            $paRef = $statusResponse['data']['pa_ref'] ?? null; // Transaction ID

            if ($paymentStatus === 'completed') {
                // 2. Payment Successful - Record Payment (similar to CI addPayment_website logic)

                // --- Record Payment Logic ---
                // Check if payment already recorded? (Implement logic to prevent duplicates)
                // Example: if (Payment::where('transaction_id', $paRef)->exists()) { ... }

                // Record payment in your system (adjust model/column names)
                $paymentRecord = new PaymentRecord();
                $paymentRecord->job_id = $workshopId; // Assuming you link to job, not invoice
                $paymentRecord->workshop_id = $workshopId; // If workshop_id is the column
                $paymentRecord->amount = $workshop->total ?? $workshop->grandTotal ?? 0; // Adjust attribute
                $paymentRecord->payment_method = 'paymentassist';
                $paymentRecord->transaction_id = $paRef;
                $paymentRecord->status = 'completed'; // Or use your status enum/column
                // Add other relevant fields (customer_id, created_at, etc. if not auto-filled)

                $paymentSaved = false;
                try {
                    $paymentSaved = $paymentRecord->save();
                } catch (\Exception $e) {
                    Log::error("PaymentAssist: Error saving payment record for Job ID {$workshopId}: " . $e->getMessage());
                }

                if ($paymentSaved) {
                    // --- Update Job/Workshop Status (similar to CI updateRecordWebsite) ---
                    try {
                        // Update job status (adjust status values and column names)
                        $workshop->status = 'completed'; // Or 'paid' or your equivalent status
                        $workshop->payment_status = 1; // Paid
                        // Update paid amount if you track it
                        $workshop->paid_price = ($workshop->paid_price ?? 0) + ($workshop->total ?? $workshop->grandTotal ?? 0);
                        $workshop->balance_price = 0; // Or recalculate
                        $workshop->save();

                        // --- Update Order Status if linked (similar to CI logic) ---
                        // if($workshop->order_id > 0 && $workshop->reference_type == 'order'){
                        //     // Update oc_order status
                        //     // Update oc_order_history
                        // }

                        // --- Update Item Quantities if applicable (similar to CI logic) ---
                        // Loop through job items and update stock

                        // --- Log Activity (similar to CI logic) ---
                        // Log the payment activity

                        Session::flash('success', 'Payment recorded successfully.');
                        Log::info("PaymentAssist website payment successful for Job ID: {$workshopId}, PA Ref: {$paRef}");

                        // Redirect to success page (similar to CI)
                        // Example: return redirect(APP_BASE_URL . 'checkout/success');
                        // Or a named route:
                        return redirect()->route('checkout.success'); // Define this route

                    } catch (\Exception $e) {
                        // Payment saved, but job/order update failed
                         Session::flash('warning', 'Payment recorded successfully with Payment Assist, but there was an issue updating the job status. Please contact support.');
                        Log::error("PaymentAssist: Payment saved but job update failed for Job ID {$workshopId}: " . $e->getMessage());
                        // Still redirect to success, but with a warning
                        // return redirect(APP_BASE_URL . 'checkout/success');
                        return redirect()->route('checkout.success'); // Adjust route
                    }

                } else {
                    // Failed to save payment record
                    Session::flash('error', 'Payment recorded successfully with Payment Assist, but failed to save record in our system. Please contact support.');
                    Log::error("PaymentAssist website payment successful but failed to save record for Job ID: {$workshopId}, PA Ref: {$paRef}");
                    // Redirect to error or support page
                    // Example: return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
                    return redirect()->route('checkout.payment.error'); // Define this route or use a generic one
                }

            } else {
                // 2. Payment Incomplete/Failed (similar to CI)
                Session::flash('warning', 'Payment was not completed successfully.');
                Log::info("PaymentAssist website payment incomplete/failed for Job ID: {$workshopId}, Status: {$paymentStatus}");
                 // Redirect back to payment or error page (similar to CI)
                // Example: return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
                return redirect()->route('checkout.payment.retry', ['jobid' => $workshopId]); // Define this route
            }

        } else {
            // Handle error from /status (similar to CI)
            $errorMessage = $statusResponse['msg'] ?? 'Failed to verify payment status.';
            Session::flash('error', 'Payment verification failed: ' . $errorMessage);
            Log::error("PaymentAssist website status check failed for Job ID: {$workshopId}", ['response' => $statusResponse]);
             // Redirect to error page (similar to CI)
            // Example: return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
            return redirect()->route('checkout.payment.retry', ['jobid' => $workshopId]); // Define this route
        }
    }

    /**
     * Helper function to get human-readable key names (from CodeIgniter example).
     * You can move this to a helper file if preferred.
     *
     * @param string $key
     * @return string
     */
    private function getKeyValue(string $key): string
    {
        // You can use match() if on PHP 8+
        // return match($key) {
        //     's_name' => 'Last Name',
        //     'f_name' => 'First Name',
        //     'addr1' => 'Address',
        //     'api_key' => 'api key',
        //     'order_id' => 'order id',
        //     default => $key,
        // };

        // Or traditional if/else for broader compatibility
        if($key == 's_name'){
            return 'Last Name';
        } elseif($key == 'f_name'){
            return 'First Name';
        } elseif($key == 'addr1'){
            return 'Address';
        } elseif($key == 'api_key'){
            return 'api key';
        } elseif($key == 'order_id'){
            return 'order id';
        } else{
            return $key;
        }
    }
}