<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentAssistService
{
    private string $apiKey;
    private string $secret;
    private string $apiUrl;
    private string $userAgent = 'Payment Assist Laravel Client';
    private string $version = 'v1.0.0';

    public function __construct()
    {
        $this->apiKey = config('services.paymentassist.api_key');
        $this->secret = config('services.paymentassist.secret');
        $testMode = config('services.paymentassist.test_mode', true);

        $this->apiUrl = $testMode
            ? rtrim(config('services.paymentassist.demo_url'), ' /')
            : rtrim(config('services.paymentassist.live_url'), ' /');
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

    // You can add other helper methods here if needed, like formatting addresses, etc.
}