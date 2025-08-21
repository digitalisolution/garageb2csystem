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
     * Display the initial payment page for website-initiated payments
     * This is the page where the user confirms they want to pay via PaymentAssist for a Workshop.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
   public function showPaymentPage(Request $request)
{
    $workshopId = $request->get('jobid');
    $hash       = $request->get('hash');
    $total      = $request->get('total');

    $workshop = Workshop::findOrFail($workshopId);

    // Recreate hash
    $secret = get_option('paymentmethod_paymentassist_Secret_key') ?? null;
    $expectedHash = hash_hmac('sha256', $workshop->id . '|' . $total, $secret);

    if ($hash !== $expectedHash) {
        abort(403, 'Invalid payment link.');
    }

    $customer = $workshop->customer;
    $billingEmail = auth()->check() && auth()->user()->customer
        ? auth()->user()->customer->email
        : ($customer->email ?? $workshop->email ?? '');

    $data = [
        'job' => $workshop,
        'total' => $total,
        'billing_email' => $billingEmail,
        'address_2_required' => false,
        'state_required' => false,
        'zip_code_required' => false,
    ];

    return view('gateways.paymentassist.payment_page', $data);
}

    /**
     * Handle the initial submission from the website payment page (similar to complete_purchase_website).
     * Checks pre-approval and redirects to PaymentAssist.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'jobid' => 'required|integer|exists:workshops,id',
            'total' => 'required|numeric|min:0.01',
        ]);

        $workshopId = $request->input('jobid');
        $total = $request->input('total');

        $workshop = Workshop::findOrFail($workshopId);
        $customer = $workshop->customer;

        if (!$customer) {
             Session::flash('error', 'Customer details not found.');
             return redirect()->route('paymentassist.pay', ['jobid' => $workshopId, 'total' => $total]);
        }

        $customerData = [
            'firstname' => $customer->customer_name ?? $customer->company_name ?? '',
            'lastname' => $customer->customer_last_name ?? '',
            'email' => $customer->customer_email ?? '',
            'telephone' => $customer->mobile ?? '',
            'address' => trim(implode(' ', [
                $workshop->address ?? '',
                $workshop->city ?? '',
                $workshop->county ?? '',
                $workshop->zone ?? '',
                $workshop->country ?? '',
            ])),
            'city' => $workshop->city ?? '',
            'postcode' => $workshop->zone ?? '',
        ];

        $preApprovalResponse = $this->paymentAssistService->checkPreApproval($customerData);
        Log::info("PaymentAssist Pre-approval check for Website Job ID: {$workshopId}", ['response' => $preApprovalResponse]);

        if (!$preApprovalResponse) {
            Session::flash('error', 'Failed to check pre-approval with Payment Assist. Please try again.');
            return redirect()->route('paymentassist.pay', ['jobid' => $workshopId, 'total' => $total]);
        }
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
        }

        if ($isApproved) {
            $callbackRoute = route('paymentassist.callback', ['invoiceid' => $workshopId]);
            $orderId = $workshopId . '-' . time();

            $paymentData = [
                'order_id' => $orderId,
                'amount' => $total,
                'firstname' => $customerData['firstname'],
                'lastname' => $customerData['lastname'],
                'email' => $customerData['email'],
                'address' => $customerData['address'],
                'postcode' => $customerData['postcode'],
                'success_url' => $callbackRoute,
                'failure_url' => $callbackRoute,
            ];
            $beginResponse = $this->paymentAssistService->beginPayment($paymentData);
            Log::info("PaymentAssist Begin Payment for Website Job ID: {$workshopId}", ['response' => $beginResponse]);

            if (!$beginResponse) {
                 Session::flash('error', 'Failed to initiate payment with Payment Assist. Please try again.');
                return redirect()->route('paymentassist.pay', ['jobid' => $workshopId, 'total' => $total]);
            }

            if (isset($beginResponse['status']) && $beginResponse['status'] === 'ok' &&
                isset($beginResponse['data']['url'])) {
                $paymentUrl = $beginResponse['data']['url'];
                Session::put('paymentassist_website_order_id', $orderId);
                Session::put('paymentassist_website_job_id', $workshopId);
                return Redirect::away($paymentUrl);

            } else {
                $errorMessage = $beginResponse['msg'] ?? 'Unknown error initiating payment.';
                if (isset($beginResponse['data']) && is_array($beginResponse['data'])) {
                    foreach ($beginResponse['data'] as $key => $val) {
                        $errorMessage .= ', ' . strtolower(str_replace("_", " ", $this->getKeyValue($key))) . ' ' . strtolower($val);
                    }
                }
                $errorMessage .= ', Cannot proceed.';
                Session::flash('error', $errorMessage);
                return redirect()->route('paymentassist.pay', ['jobid' => $workshopId, 'total' => $total]);
                // return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
            }

        } else {
            Session::flash('error', $errorMessage);
            return redirect()->route('paymentassist.pay', ['jobid' => $workshopId, 'total' => $total]);
            // return redirect(APP_BASE_URL . 'checkout/checkout/re_payment?jobid=' . $workshopId);
        }
    }

    /**
     * Handle the callback from PaymentAssist for website-initiated payments
     *
     * @param Request $request
     * @param int $workshopId
     * @return \Illuminate\Http\RedirectResponse
     */
  public function handleCallback(Request $request, int $workshopId)
{
    $token = $request->get('token');

    if (!$token) {
        Log::warning("PaymentAssist callback received without token for Job ID: {$workshopId}");
        Session::flash('error', 'Invalid callback from Payment Assist.');
        return redirect()->route('checkout.payment.error');
    }

    $workshop = Workshop::find($workshopId);
    if (!$workshop) {
        Session::flash('error', 'Job not found.');
        return redirect()->route('checkout.payment.error');
    }

    $statusResponse = $this->paymentAssistService->checkStatus($token);
    Log::info("PaymentAssist status check for Job ID: {$workshopId}", ['response' => $statusResponse]);

    if (!$statusResponse || !isset($statusResponse['status']) || $statusResponse['status'] !== 'ok') {
        $errorMessage = $statusResponse['msg'] ?? 'Failed to verify payment status with Payment Assist.';
        Session::flash('error', $errorMessage);
        Log::error("PaymentAssist: status check failed for Job ID {$workshopId}", ['response' => $statusResponse]);
        return redirect()->route('checkout.payment.retry', ['jobid' => $workshopId]);
    }

    $paymentStatus = $statusResponse['data']['status'] ?? null;
    $paRef         = $statusResponse['data']['pa_ref'] ?? null;

    $data = [
        'workshop_id'    => $workshopId,
        'transactionid' => $paRef,
        'status'        => $paymentStatus,
    ];

    $result = $this->paymentAssistService->addPaymentWebsite($data);

    if ($result['payment_status'] === 'success') {
        Session::flash('success', 'Payment recorded successfully.');
        return redirect()->route('checkout.ordersuccess');
    }

    if ($paymentStatus === 'completed') {
        Session::flash('warning', 'Payment was successful but we could not update our records. Please contact support.');
        return redirect()->route('checkout.ordersuccess');
    }

    // Payment not completed
    Session::flash('error', 'Payment was not completed.');
    return redirect()->route('checkout.orderFailure');
}


    /**
     * Helper function to get human-readable key names
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