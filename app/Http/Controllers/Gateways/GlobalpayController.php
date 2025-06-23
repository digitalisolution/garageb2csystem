<?php

namespace App\Http\Controllers\Gateways;

use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Services\GlobalPayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class GlobalpayController extends Controller
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
            ? 'https://hpp.sandbox.realexpayments.com/pay'
            : 'https://hpp.globaliris.com/pay';

        // Prepare client-related data if available


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
        $currency = config('globalpay.currency');
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
        \Log::info('Globalpay Response: ' . json_encode($resData));

        $hash = $this->verifyHash($resData);
        list($reference, $refId) = explode('-', explode('T', $resData['ORDER_ID'])[0]);
        $paymentStatus = $resData['RESULT'];
        $transactionId = $resData['PASREF'] ?? '';
        $amount = $resData['AMOUNT'] / 100;

        if ($hash !== $resData['SHA1HASH']) {
            return Redirect::route('checkout.repayment', ['workshopid' => $refId])->with('error', __('online_payment_not_valid'));
        }

        if ($paymentStatus == '00') {
            $this->globalpay->addPaymentWebsite([
                'status' => 'success',
                'workshopid' => $refId,
                'amount' => $amount,
                'transactionid' => $transactionId,
            ]);

            return Redirect::route('checkout.success')->with('success', __('online_payment_recorded_success'));
        }

        $errorMessages = [
            '101' => 'online_payment_decline_by_bank',
            '102' => 'online_payment_decline_by_bank',
            '103' => 'online_payment_decline_by_bank',
            '501' => 'online_payment_already_processed',
            '502' => 'online_payment_compulsory_field_not_present',
            '508' => 'online_payment_invalid_hash',
        ];

        $errorMessage = $errorMessages[$paymentStatus] ?? 'online_payment_generic_error';
        return Redirect::route('checkout.repayment', ['workshopid' => $refId])->with('error', __($errorMessage));
    }
}
