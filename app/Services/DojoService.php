<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Workshop;
use App\Models\PaymentRecord;
use App\Models\CustomerDebitLog;
use App\Models\PaymentHistory;
use App\Services\ApiOrderingService;
use App\Services\UpdateOrderQtyService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DojoService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.dojo.tech';
    protected $apiOrderingService;
    protected $updateOrderQtyService;

    public function __construct(ApiOrderingService $apiOrderingService,UpdateOrderQtyService $updateOrderQtyService)
    {
        $this->apiKey = get_option('paymentmethod_dojo_test_mode_enabled') ? get_option('paymentmethod_dojo_test_api_key') : get_option('paymentmethod_dojo_live_api_key');
        $this->apiOrderingService = $apiOrderingService;
        $this->updateOrderQtyService = $updateOrderQtyService;
    }

    public function processPaymentWebsite($data)
    {
        $workshopId = $data['workshop_id'];
        $encodedworkshopId = base64_encode($workshopId);

        // Redirect to Dojo payment gateway
        return redirect()->route('dojo.make-payment', ['workshopid' => $encodedworkshopId]);
    }

    public function createPaymentIntent($data, $key)
    {
        try {
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $key, // Use Basic auth
                'Content-Type' => 'application/json',
                'version' => date('Y-m-d'),
            ])->post("{$this->baseUrl}/payment-intents", $data);

            // Check for HTTP errors
            if ($response->failed()) {
                
                return null;
            }

            // Decode the JSON response
            $jsonResponse = $response->json();

            return $jsonResponse;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPaymentIntent($id, $key)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $key,
            'version' => date('Y-m-d'),
        ])->get("{$this->baseUrl}/payment-intents/{$id}");

        return $response->json();
    }

    public function addPaymentWebsite($data)
    {
        if (!empty($data)) {
            $workshop = Workshop::find($data['workshopid']);
    
            if ($data['status'] == 'paid') {
                // Create a payment record
                $paymentRecord = PaymentRecord::create([
                    'workshop_id' => $data['workshopid'],
                    'amount' => $workshop->grandTotal,
                    'paymentmode' => 'dojo',
                    'transactionid' => $data['transactionid'],
                    'date' => Carbon::now('Europe/London')->format('Y-m-d'),
                    'daterecorded' => Carbon::now('Europe/London')->format('Y-m-d H:i:s')
                ]);
                $paymentHistory = PaymentHistory::create([
                    'job_id' => $data['workshopid'],
                    'payment_date' => Carbon::now('Europe/London')->format('Y-m-d'),
                    'payment_amount' => $workshop->grandTotal,
                ]);
                $customerId = $workshop->customer_id;

                CustomerDebitLog::create([
                    'customer_id' => $customerId,
                    'workshop_id' => $data['workshopid'],
                    'payment_history_id' => $paymentHistory->id ?? null,
                    'created_at' => Carbon::now('Europe/London')->format('Y-m-d H:i:s'),
                    'debit_amount' => $workshop->grandTotal,
                    'is_debit' => 1,
                    'comments' => 'Online Payment via Dojo',
                    'payment_type' => 2,
                ]);
    
                // Update workshop record
                $this->updateRecordWebsite($data['workshopid'], $paymentRecord->id, $workshop->grandTotal);
                $this->apiOrderingService->processApiOrder($data['workshopid']);
                $this->updateOrderQtyService->updateStockQty($data['workshopid']);

                $validated = [
                    'customer_name' => $workshop->name ?? 'Customer Name Not Available',
                    'email' => $workshop->email ?? 'No Email Available',
                ];
                $this->sendOrderConfirmationEmail($validated, $data['workshopid']);
                foreach (Session::all() as $key => $value) {
                if (Str::startsWith($key, 'login_customer_')) {
                    continue;
                }
                Session::forget($key);
                }
                return [
                    'payment_record_id' => $paymentRecord->id,
                    'payment_status' => 'success',
                ];
            } else {
                $validated = [
                    'customer_name' => $workshop->name ?? 'Customer Name Not Available',
                    'email' => $workshop->email ?? 'No Email Available',
                ];
                $this->sendOrderConfirmationEmail($validated, $data['workshopid']);
    
                return [
                    'payment_record_id' => 0,
                    'payment_status' => 'failed',
                ];
            }
        }
    }

    public function updateRecordWebsiteFailed($workshopId, $amount)
    {
        // Update workshop status to indicate failure
        Workshop::where('id', $workshopId)->update(['status' => 0]);
    
        // Retrieve workshop details for email
        $workshop = Workshop::find($workshopId);
        $validated = [
            'customer_name' => $workshop->name ?? 'Customer Name Not Available',
            'email' => $workshop->email ?? 'No Email Available',
        ];
    
        // Send order confirmation email for failed payment
        $this->sendOrderConfirmationEmail($validated, $workshopId);
    }

    public function updateRecordWebsite($workshopId, $paymentRecordId, $amount)
    {
        Workshop::where('id', $workshopId)->update([
            'payment_status' => 1,
            'paid_price' => $amount,
            'balance_price' => 0.00,
            'status' => 'booked'
        ]);
    }


    protected function sendOrderConfirmationEmail(array $validated, $orderId, $paymentStatus = 'success')
    {
        try {
            // Customer details
            $customer = [
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
            ];
    
            // Retrieve garage details
            $garage = \App\Models\GarageDetails::first();
            $garageEmail = $garage->email;
            $garageName = $garage ? $garage->garage_name : config('mail.from.name');
    
            // Email subject based on payment status
            $subject = $paymentStatus === 'success' ? "Order Confirmation - Payment Successful" : "Order Confirmation - Payment Failed";
    
            // Send email to the customer
            Mail::to($customer['email'])->send(new SendMailToCustomer($orderId, $customer, $garage));

            // Send email to the owner
            $ownerEmail = 'info@digitalideasltd.co.uk';
            Mail::to($ownerEmail)->send(new OrderToGarage($orderId, $customer, $garage));

            // Send email to the garage
            if ($garageEmail) {
                Mail::to($garageEmail)->send(new OrderToGarage($orderId, $customer, $garage));
            } else {
                Log::warning('Garage email not found.', ['email' => $garageEmail]);
            }
        } catch (\Exception $e) {
            Log::error('Error during email submission.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

}