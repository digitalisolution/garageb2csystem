<?php

namespace App\Http\Controllers\Gateways;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payment;
use App\Services\PaymentAssistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class PaymentAssistController extends Controller
{
    protected PaymentAssistService $paymentAssistService;

    public function __construct(PaymentAssistService $paymentAssistService)
    {
        $this->paymentAssistService = $paymentAssistService;
    }

    /**
     * Display the initial payment page (similar to make_payment).
     * This is the page where the user confirms they want to pay via PaymentAssist.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function showPaymentPage(Request $request)
    {
        $invoiceId = $request->get('invoiceid');
        $hash = $request->get('hash');
        $total = $request->get('total');

        // check_invoice_restrictions($invoiceId, $hash);
        $invoice = Invoice::find($invoiceId);
        if (!$invoice || $invoice->hash !== $hash) {
             // Log error or redirect
            abort(404, 'Invoice not found or invalid hash.');
        }

        // Load customer language if applicable
        // load_client_language($invoice->customer_id);

        $data = [
            'invoice' => $invoice,
            'total' => $total,
            // Add other data needed for your view (address flags, etc.)
        ];

        return view('payments.paymentassist.payment_page', $data); // Create this view
    }

    /**
     * Handle the initial submission from the payment page to check pre-approval and redirect.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'invoiceid' => 'required|integer|exists:invoices,id',
            'total' => 'required|numeric|min:0.01',
        ]);

        $invoiceId = $request->input('invoiceid');
        $total = $request->input('total');

        // Get invoice and customer details
        $invoice = Invoice::findOrFail($invoiceId); // Adjust model/retrieval
        $customer = $invoice->customer; // Assuming relationship exists

        if (!$customer) {
             Session::flash('error', 'Customer details not found.');
            return Redirect::back();
        }

        // Prepare customer data for pre-approval check
        $customerData = [
            'firstname' => $customer->first_name ?? '', // Adjust attribute names
            'lastname' => $customer->last_name ?? '',
            'address' => trim(implode(' ', [
                $invoice->billing_street ?? '',
                $invoice->billing_city ?? '',
                $invoice->billing_state ?? '',
                $invoice->billing_zip ?? '',
                // Add country name if needed
            ])),
            'postcode' => $invoice->billing_zip ?? '',
        ];

        // 1. Check Pre-approval
        $preApprovalResponse = $this->paymentAssistService->checkPreApproval($customerData);

        // Log the pre-approval attempt
        Log::info("PaymentAssist Pre-approval check for Invoice ID: {$invoiceId}", ['response' => $preApprovalResponse]);

        if (!$preApprovalResponse) {
            Session::flash('error', 'Failed to check pre-approval with Payment Assist. Please try again.');
            return Redirect::back();
        }

        if (isset($preApprovalResponse['status']) && $preApprovalResponse['status'] === 'ok' &&
            isset($preApprovalResponse['data']['approved']) && $preApprovalResponse['data']['approved'] == 1) {

            // 2. Pre-approved, proceed to begin payment
            $orderId = $invoiceId . '-' . time(); // Or generate a unique order ID

            // Prepare payment data
            $paymentData = [
                'order_id' => $orderId,
                'amount' => $total,
                'firstname' => $customerData['firstname'],
                'lastname' => $customerData['lastname'],
                'email' => $customer->email ?? '', // Adjust attribute
                'address' => $customerData['address'],
                'postcode' => $customerData['postcode'],
                'success_url' => URL::route('paymentassist.callback', ['invoiceid' => $invoiceId]),
                'failure_url' => URL::route('paymentassist.callback', ['invoiceid' => $invoiceId]),
            ];

            // 3. Begin Payment
            $beginResponse = $this->paymentAssistService->beginPayment($paymentData);

            // Log the begin payment attempt
            Log::info("PaymentAssist Begin Payment for Invoice ID: {$invoiceId}", ['response' => $beginResponse]);

            if (!$beginResponse) {
                 Session::flash('error', 'Failed to initiate payment with Payment Assist. Please try again.');
                return Redirect::back();
            }

            if (isset($beginResponse['status']) && $beginResponse['status'] === 'ok' &&
                isset($beginResponse['data']['url'])) {

                // 4. Redirect to Payment Assist
                $paymentUrl = $beginResponse['data']['url'];
                // Store the order ID or token in session/database for later verification if needed
                Session::put('paymentassist_order_id', $orderId);
                return Redirect::away($paymentUrl);

            } else {
                // Handle error from /begin
                $errorMessage = $beginResponse['msg'] ?? 'Unknown error initiating payment.';
                if (isset($beginResponse['data']) && is_array($beginResponse['data'])) {
                    foreach ($beginResponse['data'] as $key => $val) {
                        // You might want a helper function like getKeyValue here
                        $errorMessage .= ', ' . strtolower(str_replace("_", " ", $key)) . ' ' . strtolower($val);
                    }
                }
                $errorMessage .= ', Cannot proceed.';
                Session::flash('error', $errorMessage);
                return Redirect::back();
            }

        } else {
            // Handle error from /preapproval
            $errorMessage = $preApprovalResponse['msg'] ?? 'Pre-approval failed.';
            if (isset($preApprovalResponse['data']) && is_array($preApprovalResponse['data'])) {
                 foreach ($preApprovalResponse['data'] as $key => $val) {
                    // You might want a helper function like getKeyValue here
                    $errorMessage .= ', ' . strtolower(str_replace("_", " ", $key)) . ' ' . strtolower($val);
                }
            }
            $errorMessage .= ', Cannot proceed.';
            Session::flash('error', $errorMessage);
            return Redirect::back();
        }
    }

    /**
     * Handle the callback from PaymentAssist after payment attempt.
     *
     * @param Request $request
     * @param int $invoiceId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleCallback(Request $request, int $invoiceId)
    {
        $token = $request->get('token');

        if (!$token) {
             Log::warning("PaymentAssist callback received without token for Invoice ID: {$invoiceId}");
             Session::flash('error', 'Invalid callback from Payment Assist.');
            // Redirect to invoice view or a generic error page
            // return Redirect::to("/invoice/{$invoiceId}/hash"); // Adjust URL
             return Redirect::to("/"); // Example fallback
        }

        // Get invoice
        $invoice = Invoice::findOrFail($invoiceId); // Adjust model/retrieval

        // 1. Check Payment Status
        $statusResponse = $this->paymentAssistService->checkStatus($token);

        // Log the status check
        Log::info("PaymentAssist Status check for Invoice ID: {$invoiceId} with token: {$token}", ['response' => $statusResponse]);

        if (!$statusResponse) {
             Session::flash('error', 'Failed to verify payment status with Payment Assist.');
            // Redirect to invoice view
            // return Redirect::to("/invoice/{$invoiceId}/hash");
            return Redirect::to("/");
        }

        if (isset($statusResponse['status']) && $statusResponse['status'] === 'ok') {

            $paymentStatus = $statusResponse['data']['status'] ?? null;
            $paRef = $statusResponse['data']['pa_ref'] ?? null; // Transaction ID

            if ($paymentStatus === 'completed') {
                // 2. Payment Successful - Record Payment

                // Check if payment already recorded? (Implement logic to prevent duplicates)
                // Example: if (Payment::where('transaction_id', $paRef)->exists()) { ... }

                // Record payment in your system
                $paymentRecord = new Payment(); // Adjust model
                $paymentRecord->invoice_id = $invoiceId; // Adjust column name
                $paymentRecord->amount = $invoice->total; // Or the amount paid if partial
                $paymentRecord->payment_method = 'paymentassist'; // Adjust column name
                $paymentRecord->transaction_id = $paRef; // Adjust column name
                $paymentRecord->status = 'completed'; // Adjust if you have a status column
                // Add other relevant fields (customer_id, created_at, etc.)

                if ($paymentRecord->save()) {
                    // 3. Update Invoice Status (if applicable)
                    // $invoice->status = 'paid'; // Adjust status logic
                    // $invoice->paid_amount = $invoice->total; // Adjust if partial payments
                    // $invoice->save();

                    // 4. Update Job/Order Status (if applicable)
                    // Implement logic similar to updateJobAndOrder in CodeIgniter

                    // 5. Update Item Quantities (if applicable)
                    // Implement logic similar to updateItemQty in CodeIgniter

                    // 6. Log Activity (if applicable)
                    // Implement logic similar to logPaymentActivity in CodeIgniter

                    Session::flash('success', 'Payment recorded successfully.');
                    Log::info("PaymentAssist payment successful for Invoice ID: {$invoiceId}, PA Ref: {$paRef}");
                } else {
                    Session::flash('error', 'Payment recorded successfully with Payment Assist, but failed to save record in our system. Please contact support.');
                    Log::error("PaymentAssist payment successful but failed to save record for Invoice ID: {$invoiceId}, PA Ref: {$paRef}");
                }

                 // Redirect to invoice view or success page
                // return Redirect::to("/invoice/{$invoiceId}/hash");
                return Redirect::to("/"); // Example success redirect

            } else {
                // 2. Payment Incomplete/Failed
                Session::flash('warning', 'Payment was not completed successfully.');
                Log::info("PaymentAssist payment incomplete/failed for Invoice ID: {$invoiceId}, Status: {$paymentStatus}");
                 // Redirect to invoice view or payment page
                // return Redirect::to("/invoice/{$invoiceId}/hash");
                return Redirect::to("/"); // Example redirect
            }

        } else {
            // Handle error from /status
            $errorMessage = $statusResponse['msg'] ?? 'Failed to verify payment status.';
            Session::flash('error', 'Payment verification failed: ' . $errorMessage);
            Log::error("PaymentAssist status check failed for Invoice ID: {$invoiceId}", ['response' => $statusResponse]);
             // Redirect to invoice view or error page
            // return Redirect::to("/invoice/{$invoiceId}/hash");
            return Redirect::to("/");
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
        return match($key) {
            's_name' => 'Last Name',
            'f_name' => 'First Name',
            'addr1' => 'Address',
            'api_key' => 'api key',
            'order_id' => 'order id',
            default => $key,
        };
    }
}