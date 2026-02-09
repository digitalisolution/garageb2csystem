<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Services\GlobalPayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $globalpay;

    public function __construct(GlobalPayService $globalpay)
    {
        $this->globalpay = $globalpay;
    }

    public function makePaymentWebsite(Request $request)
    {
        // Fetch the workshop details based on the query parameter 'workshopid'
        $workshop = Workshop::findOrFail($request->query('workshopid'));

        // Check if the payment has already been completed
        if ($workshop->payment_status === 1) {
            return response()->make("
            <h2 style='color: red;'>Payment Link Expired!</h2>
            <p>This payment link has already been used.</p>
            <script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = '" . route('home') . "';
                }, 3000);
            </script>
        ", 200, ['Content-Type' => 'text/html']);
        }

        // Generate a unique order ID
        $timestamp = now()->format('YmdHis');
        $orderId = 'job-' . $workshop->id . 'T' . $timestamp . mt_rand(1, 999);

        // Calculate the amount in cents (or smallest currency unit)
        $amount = round($workshop->grandTotal * 100);

        // Retrieve the currency and merchant ID from settings or configuration
        $currency = get_option('paymentmethod_globalpay_currencies'); // Replace with your actual method to fetch currency
        $merchantId = $this->globalpay->merchantId(); // Replace with your actual method to fetch merchant ID

        // Generate the hash for security verification
        $hash = $this->verifyHash([], $timestamp, $orderId, $amount);

        // Define the success callback URL
        $successUrl = route('payment.callback');

        // Determine the payment gateway action URL based on the test mode setting
        $action = get_option('paymentmethod_globalpay_globalpay_test')
            ? 'https://hpp.sandbox.globaliris.com/pay'
            : 'https://hpp.globaliris.com/pay';

        // Pass all necessary data to the view
        return view('payment', compact(
            'workshop',
            'merchantId',
            'orderId',
            'currency',
            'amount',
            'timestamp',
            'hash',
            'successUrl',
            'action',
        ));
    }

    public function verifyHash($data = [], $timestamp = '', $orderId = '', $amount = '')
    {
        $currency = get_option('paymentmethod_globalpay_currencies');
        $merchantId = $this->globalpay->merchantId();
        $secretKey = $this->globalpay->secreteKey();

        if (empty($data)) {
            $tmp = sha1("$timestamp.$merchantId.$orderId.$amount.$currency");
        } else {
            $tmp = sha1("{$data['TIMESTAMP']}.$merchantId.{$data['ORDER_ID']}.{$data['RESULT']}.{$data['MESSAGE']}.{$data['PASREF']}.{$data['AUTHCODE']}");
        }

        return sha1("$tmp.$secretKey");
    }


    public function callback(Request $request)
    {
        $resData = $request->all();
        // \Log::info('Globalpay Response: ' . json_encode($resData));

        // Validate required fields in the response
        if (empty($resData['ORDER_ID']) || empty($resData['RESULT']) || empty($resData['AMOUNT'])) {
            \Log::error('Invalid response data: Missing required fields.');
            return Redirect::route('checkout.repayment', ['workshopid' => $refId ?? 0])
                ->with('error', __('online_payment_invalid_response'));
        }

        // Verify the hash
        $hash = $this->verifyHash($resData);
        if ($hash !== $resData['SHA1HASH']) {
            \Log::error('Hash verification failed.');
            return Redirect::route('checkout.repayment', ['workshopid' => $refId ?? 0])
                ->with('error', __('online_payment_not_valid'));
        }

        // Extract reference and workshop ID from ORDER_ID
        list($reference, $refId) = explode('-', explode('T', $resData['ORDER_ID'])[0]);
        $paymentStatus = $resData['RESULT'];
        $transactionId = $resData['PASREF'] ?? '';
        $amount = $resData['AMOUNT'] / 100;

        // Handle successful payment
        if ($paymentStatus == '00') {
            $this->globalpay->addPaymentResponse([
                'status' => 'success',
                'workshopid' => $refId,
                'amount' => $amount,
                'transactionid' => $transactionId,
            ]);

            // Update the workshop status to 'paid'
            Workshop::where('id', $refId)->update(['payment_status' => 1]);

            // Clear session and set payment success flag
            // Session::flush();
            foreach (Session::all() as $key => $value) {
                // Skip if the key starts with 'login_customer_'
                if (Str::startsWith($key, 'login_customer_')) {
                    continue;
                }
                // Forget all other keys
                Session::forget($key);
            }
            session()->put('payment_success', true);

            // Redirect to success page
            $redirectUrl = route('checkout.ordersuccess');
            return response()->make("
            <h2 style='color: green;'>Payment Successful!</h2>
            <p>Please click <a href='{$redirectUrl}'>here</a> to continue.</p>
            <script type='text/javascript'>
                setTimeout(function() {
                    window.location.href = '{$redirectUrl}';
                }, 3000);
            </script>
        ", 200, ['Content-Type' => 'text/html']);
        }

        // Handle payment failure
        $this->globalpay->addPaymentResponse([
            'status' => 'failed',
            'workshopid' => $refId,
        ]);

        $errorMessages = [
            '101' => 'online_payment_decline_by_bank',
            '102' => 'online_payment_decline_by_bank',
            '103' => 'online_payment_decline_by_bank',
            '110' => 'online_payment_decline_by_bank',
            '501' => 'online_payment_already_processed',
            '502' => 'online_payment_compulsory_field_not_present',
            '508' => 'online_payment_invalid_hash',
        ];

        // Default error message for unknown payment statuses
        $errorMessage = $errorMessages[$paymentStatus] ?? 'online_payment_generic_error';

        // Log the payment failure
        \Log::error("Payment failed with status: {$paymentStatus}. Error: {$errorMessage}");

        // Redirect to payment retry page
        $redirectUrl = route('payment.make', ['workshopid' => $refId]);
        return response()->make("
        <h2 style='color: red;'>Payment Failed!</h2>
        <p>Error: {$errorMessage}</p>
        <p>Please click <a href='{$redirectUrl}'>here</a> to retry.</p>
        <script type='text/javascript'>
            setTimeout(function() {
                window.location.href = '{$redirectUrl}';
            }, 3000);
        </script>
    ", 200, ['Content-Type' => 'text/html']);
    }

}
