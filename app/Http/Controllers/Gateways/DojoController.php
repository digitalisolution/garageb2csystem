<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Services\DojoService;
use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class DojoController extends Controller
{
    protected $dojoService;

    public function __construct(DojoService $dojoService)
    {
        $this->dojoService = $dojoService;
    }

    public function makePaymentWebsite(Request $request)
    {
        $decodedworkshopId = base64_decode($request->query('workshopid'));
        $workshop = Workshop::find($decodedworkshopId);

        if (!$workshop || $workshop->id != $decodedworkshopId) {
            return redirect()->route('checkout.checkout');
        }

        $data['workshop'] = $workshop;
        return $this->getHtmlWebsite($data);
    }

    protected function getHtmlWebsite($data = [])
    {
        $workshop = $data['workshop'];
        $orderId = $workshop->id;

        $key = get_option('paymentmethod_dojo_test_mode_enabled') ? get_option('paymentmethod_dojo_test_api_key') : get_option('paymentmethod_dojo_live_api_key');
        $currency = get_option('paymentmethod_dojo_currencies');
        $callbackUrl = route('dojo.callback');
        $cancelUrl = route('checkout.repayment', ['workshopid' => $workshop->id]);
        $total = $workshop->grandTotal * 100;

        $paymentRequest = [
            "amount" => ["currencyCode" => $currency, "value" => $total],
            "reference" => "workshop #{$orderId}",
            "config" => [
                "redirectUrl" => $callbackUrl,
                "cancelUrl" => $cancelUrl
            ]
        ];

        // Log::info('Dojo Payment Request:', $paymentRequest);
        // Call createPaymentIntent
        $response = $this->dojoService->createPaymentIntent($paymentRequest, $key);

        // Check if the response is valid and contains an 'id'
        if ($response && isset($response['id'])) {
            return redirect("https://pay.dojo.tech/checkout/{$response['id']}");
        } else {
            Log::error('Dojo Payment Initialization Failed:', [
                'response' => $response,
            ]);
            return redirect()->route('checkout')->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    public function callback(Request $request)
    {
        $key = get_option('paymentmethod_dojo_test_mode_enabled') ? get_option('paymentmethod_dojo_test_api_key') : get_option('paymentmethod_dojo_live_api_key');
        $id = $request->query('id');

        $response = $this->dojoService->getPaymentIntent($id, $key);

        if (empty($response)) {
            return redirect()->route('checkout')->with('error', 'Payment verification failed.');
        }

        // Log::info('Dojo Payment Response:', $response);

        $orderIdArray = explode("#", $response['reference']);
        $orderId = $orderIdArray[1];
        $orderType = $orderIdArray[0];

        $transactionAmount = $response['amount']['value'] / 100;
        $transactionId = $response['paymentDetails']['transactionId'];

        if (trim($orderType) == 'workshop') {
            session(['workshop_id' => $orderId]);

            if ($response['status'] == 'Captured') {
                $this->dojoService->addPaymentWebsite([
                    'status' => 'paid',
                    'workshopid' => $orderId,
                    'amount' => $transactionAmount,
                    'transactionid' => $transactionId,
                ]);

                return redirect()->route('checkout.ordersuccess')->with('success', 'Payment recorded successfully.');
            } else {
                $this->dojoService->updateRecordWebsiteFailed($orderId, $transactionAmount);
                return redirect()->route('checkout.repayment', ['workshopid' => $orderId])->with('error', 'Payment failed.');
            }
        } elseif (trim($orderType) == 'Invoice') {
            $invoice = Invoice::find($orderId);

            if ($response['status'] == 'Captured') {
                $this->dojoService->addPayment([
                    'status' => 'paid',
                    'invoiceid' => $orderId,
                    'amount' => $transactionAmount,
                    'transactionid' => $transactionId,
                ]);

                return redirect()->route('invoice.show', ['id' => $orderId, 'hash' => $invoice->hash])->with('success', 'Payment successful.');
            } else {
                return redirect()->route('invoice.show', ['id' => $orderId, 'hash' => $invoice->hash])->with('error', 'Payment failed.');
            }
        }
    }
}