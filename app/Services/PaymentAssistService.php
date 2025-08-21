<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Workshop;
use App\Models\PaymentRecord;
use App\Models\PaymentHistory;
use App\Models\CustomerDebitLog;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;

class PaymentAssistService
{
    private string $apiKey;
    private string $secret;
    private string $apiUrl;
    private string $userAgent = 'Payment Assist Laravel Client';
    private string $version = 'v1.0.0';

    public function __construct()
    {
        $this->apiKey = get_option('paymentmethod_paymentassist_api_key') ?? null;
        $this->secret = get_option('paymentmethod_paymentassist_Secret_key') ?? null;
        $testMode = get_option('paymentmethod_paymentassist_test_mode_enabled') ?? null;

        $this->apiUrl = $testMode ? rtrim('https://api.demo.payassi.st') : rtrim('https://api.demo.payassi.st');
    }

    /**
     * Generate the signature for API requests.
     *
     * @param array $params
     * @return string
     */
    public function generateSignature(array $params): string
    {
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) {
            $k = strtoupper($k);
            if ($k !== 'SIGNATURE' && $k !== 'API_KEY') {
                $str .= $k . '=' . $v . '&';
            }
        }
        return hash_hmac('sha256', $str, $this->secret, false);
    }

    /**
     * Make a request to the PaymentAssist API.
     *
     * @param string $path
     * @param string $method
     * @param array|null $params
     * @return array|null
     */
    public function makeRequest(string $path, string $method = 'POST', ?array $params = []): ?array
    {
        $params = $params ?? []; // Ensure params is an array
        $signature = $this->generateSignature($params);

        $params['api_key'] = $this->apiKey;
        $params['signature'] = $signature;

        $url = $this->apiUrl . '/' . ltrim($path, '/');

        try {
            $response = Http::withOptions([
                'verify' => false, // Consider enabling SSL verification in production
            ])->withUserAgent($this->userAgent . ' ' . $this->version)->{$method}($url, $params);

            // Log the request and response for debugging
            Log::debug("PaymentAssist Request: {$method} {$url}", ['params' => $params]);
            Log::debug("PaymentAssist Response: Status {$response->status()}", ['body' => $response->body()]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error("PaymentAssist API Error: Status {$response->status()}", ['body' => $response->body()]);
                return null; // Or throw an exception based on your error handling strategy
            }
        } catch (\Exception $e) {
            Log::error("PaymentAssist Request Exception: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return null; // Or throw an exception
        }
    }

    /**
     * Check pre-approval for a customer.
     *
     * @param array $customerData ['firstname', 'lastname', 'address', 'postcode']
     * @return array|null API response or null on failure
     */
    public function checkPreApproval(array $customerData): ?array
    {
        $params = [
            'f_name' => $customerData['firstname'] ?? '',
            's_name' => $customerData['lastname'] ?? '',
            'addr1' => $customerData['address'] ?? '',
            'postcode' => $customerData['postcode'] ?? '',
        ];

        return $this->makeRequest('/preapproval', 'POST', $params);
    }

    /**
     * Begin the payment process.
     *
     * @param array $paymentData ['order_id', 'amount', 'firstname', 'lastname', 'email', 'address', 'postcode', 'success_url', 'failure_url']
     * @return array|null API response or null on failure
     */
    public function beginPayment(array $paymentData): ?array
    {
        // Amount should be in pence/cents
        $amountInPence = bcmul((string)($paymentData['amount'] ?? 0), '100', 0); // Use bcmath for precision

        $params = [
            'order_id' => $paymentData['order_id'] ?? Str::uuid(), // Generate a unique ID if not provided
            'amount' => $amountInPence,
            'f_name' => $paymentData['firstname'] ?? '',
            's_name' => $paymentData['lastname'] ?? '',
            'email' => $paymentData['email'] ?? '',
            'addr1' => $paymentData['address'] ?? '',
            'postcode' => $paymentData['postcode'] ?? '',
            'success_url' => $paymentData['success_url'] ?? url('/'), // Provide defaults
            'failure_url' => $paymentData['failure_url'] ?? url('/'),
        ];

        return $this->makeRequest('/begin', 'POST', $params);
    }

    /**
     * Check the status of a payment using the token.
     *
     * @param string $token
     * @return array|null API response or null on failure
     */
    public function checkStatus(string $token): ?array
    {
        $params = [
            'token' => $token,
        ];

        return $this->makeRequest('/status', 'POST', $params);
    }



    /**
     * Record the payment in the system after PaymentAssist callback
     *
     * @param array $data ['workshopid', 'transactionid', 'status']
     * @return array ['payment_record_id' => int, 'payment_status' => 'success|failed']
     */
    public function addPaymentWebsite(array $data): array
    {
        $workshopId = $data['workshopid'] ?? null;
        $transactionId = $data['transactionid'] ?? null;
        $status = $data['status'] ?? null;

        if (!$workshopId || !$status) {
            Log::warning('PaymentAssist addPaymentWebsite missing required data', $data);
            return ['payment_record_id' => 0, 'payment_status' => 'failed'];
        }

        $workshop = Workshop::find($workshopId);
        if (!$workshop) {
            Log::error("Workshop not found in PaymentAssist addPaymentWebsite for ID: {$workshopId}");
            return ['payment_record_id' => 0, 'payment_status' => 'failed'];
        }

        // Only process if payment completed
        if ($status === 'completed') {
            try {
                // 1. Record Payment
                $paymentRecord = PaymentRecord::create([
                    'workshop_id'     => $workshopId,
                    'amount'          => $workshop->grandTotal ?? 0,
                    'payment_method'  => 'paymentassist',
                    'transaction_id'  => $transactionId,
                    'status'          => 'completed',
                    'date'            => Carbon::now()->format('Y-m-d'),
                    'daterecorded'    => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

                // 2. Update Workshop
                $workshop->update([
                    'payment_status' => 1,
                    'paid_price'     => ($workshop->paid_price ?? 0) + ($workshop->grandTotal ?? 0),
                    'balance_price'  => 0,
                    'status'         => 'booked', // or 'completed' if appropriate
                ]);

                // 3. Record PaymentHistory
                $paymentHistory = PaymentHistory::create([
                    'job_id'        => $workshopId,
                    'payment_date'  => Carbon::now()->format('Y-m-d'),
                    'payment_amount'=> $workshop->grandTotal ?? 0,
                ]);

                // 4. Record CustomerDebitLog
                CustomerDebitLog::create([
                    'customer_id'         => $workshop->customer_id,
                    'workshop_id'         => $workshopId,
                    'payment_history_id'  => $paymentHistory->id ?? null,
                    'created_at'          => Carbon::now()->format('Y-m-d H:i:s'),
                    'debit_amount'        => $workshop->grandTotal ?? 0,
                    'is_debit'            => 1,
                    'comments'            => 'Online Payment via PaymentAssist',
                    'payment_type'        => 2, // 2 = online payment
                ]);

                // 5. Send emails
                $this->sendOrderConfirmationEmail($workshop);

                // 6. Clear session (except login_customer)
                foreach (Session::all() as $key => $value) {
                    if (str_starts_with($key, 'login_customer_')) continue;
                    Session::forget($key);
                }

                return [
                    'payment_record_id' => $paymentRecord->id,
                    'payment_status'    => 'success',
                ];
            } catch (\Exception $e) {
                Log::error("PaymentAssist: Failed to save payment for Workshop ID {$workshopId}", [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return ['payment_record_id' => 0, 'payment_status' => 'failed'];
            }
        }

        // Payment not completed
        Log::info("PaymentAssist: Payment not completed for Workshop ID {$workshopId}", $data);
        return ['payment_record_id' => 0, 'payment_status' => 'failed'];
    }

    /**
     * Send confirmation emails to customer and garage
     */
    protected function sendOrderConfirmationEmail(Workshop $workshop)
    {
        try {
            $customerEmail = $workshop->customer->email ?? $workshop->email ?? null;
            $garage = \App\Models\GarageDetails::first();

            if ($customerEmail) {
                Mail::to($customerEmail)->send(new SendMailToCustomer($workshop->id, ['customer_name' => $workshop->name], $garage));
            }

            $ownerEmail = 'info@digitalideasltd.co.uk';
            Mail::to($ownerEmail)->send(new OrderToGarage($workshop->id, ['customer_name' => $workshop->name], $garage));

            if ($garage && $garage->email) {
                Mail::to($garage->email)->send(new OrderToGarage($workshop->id, ['customer_name' => $workshop->name], $garage));
            }
        } catch (\Exception $e) {
            Log::error("PaymentAssist: Error sending confirmation emails", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }


}