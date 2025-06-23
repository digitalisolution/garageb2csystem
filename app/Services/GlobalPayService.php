<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\PaymentRecord;
use App\Models\GeneralSettings;
use App\Models\Workshop;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\CustomerDebitLog;
use Carbon\Carbon;
use App\Services\ApiOrderingService;
use App\Services\UpdateOrderQtyService;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;
use Illuminate\Support\Facades\Mail;

class GlobalPayService
{
    protected $merchantId;
    protected $secretKey;
    protected $testMode;
    protected $apiOrderingService;
    protected $updateOrderQtyService;

    public function __construct(ApiOrderingService $apiOrderingService,UpdateOrderQtyService $updateOrderQtyService)
    {
        $this->merchantId = get_option('paymentmethod_globalpay_globalpay_merchant_id') ?? null;
        $this->secretKey = get_option('paymentmethod_globalpay_globalpay_secrete_key') ?? null;
        $this->testMode = get_option('paymentmethod_globalpay_globalpay_test') ?? '1'; // Default to test mode
        $this->apiOrderingService = $apiOrderingService;
        $this->updateOrderQtyService = $updateOrderQtyService;
    }

    public function merchantId()
    {
        return $this->merchantId;
    }

    public function secreteKey()
    {
        return $this->secretKey;
    }

    public function processWebsitePayment($workshopId, $amount)
    {
        $redirectUrl = route('globalpay.website_payment', [
            'workshopid' => $workshopId,
            'total' => $amount,
            'hash' => uniqid()
        ]);
        return redirect($redirectUrl);
    }

    public function addPaymentResponse($data)
    {
        if (!$data) {
            return [
                'paymentRecordId' => 0,
                'payment_status' => 'failed'
            ];
        }

        if ($data['status'] === 'success') {
            $payment = PaymentRecord::create([
                'workshop_id' => $data['workshopid'],
                'amount' => $data['amount'],
                'paymentmode' => 'globalpay',
                'transactionid' => $data['transactionid'],
                'date' => Carbon::now('Europe/London')->format('Y-m-d'),
                'daterecorded' => Carbon::now('Europe/London')->format('Y-m-d H:i:s')
            ]);

            if ($payment) {
                $this->updateWebsiteRecord($data['workshopid'], $payment->id, $data['amount']);

                // Create PaymentHistory
                $paymentHistory = PaymentHistory::create([
                    'job_id' => $data['workshopid'],
                    'payment_date' => Carbon::now('Europe/London')->format('Y-m-d'),
                    'payment_amount' => $data['amount'],
                ]);

                // Create CustomerDebitLog
                $workshop = Workshop::find($data['workshopid']);
                if ($workshop && $workshop->customer_id) {
                    CustomerDebitLog::create([
                        'customer_id' => $workshop->customer_id,
                        'workshop_id' => $data['workshopid'],
                        'payment_history_id' => $paymentHistory->id ?? null,
                        'debit_amount' => $data['amount'],
                        'is_debit' => 1,
                        'comments' => 'Online Payment via GlobalPay',
                        'payment_type' => 2,
                    ]);
                }
                $this->apiOrderingService->processApiOrder($data['workshopid']);
                $this->updateOrderQtyService->updateStockQty($data['workshopid']);
                
                $validated = [
                    'customer_name' => $workshop->name ?? 'Customer Name Not Available',
                    'email' => $workshop->email ?? 'No Email Available',
                ];

                $this->sendOrderConfirmationEmail($validated, $data['workshopid']);
            }
            foreach (Session::all() as $key => $value) {
                if (Str::startsWith($key, 'login_customer_')) {
                    continue;
                }
                Session::forget($key);
            }
            return [
                'paymentRecordId' => $payment->id,
                'payment_status' => 'success'
            ];
        } elseif ($data['status'] === 'failed') {
            $workshop = Workshop::find($data['workshopid']);
            $validated = [
                'customer_name' => $workshop->name ?? 'Customer Name Not Available',
                'email' => $workshop->email ?? 'No Email Available',
            ];
            $this->updateBookingStatus( $validated, $data['workshopid']);
        }

        return [
            'paymentRecordId' => 0,
            'payment_status' => 'failed'
        ];
    }

    public function updateWebsiteRecord($workshopId, $paymentRecordId, $amount)
    {
        if ($paymentRecordId) {
            $workshop = Workshop::find($workshopId);
            if ($workshop) {
                $workshop->update([
                    'payment_status' => 1,
                    'paid_price' => $amount,
                    'balance_price' => 0.00,
                    'status' => 'booked'
                ]);
            }
        }
    }

    public function updateBookingStatus($validated, $workshopId)
    {
        $workshop = Workshop::find($workshopId);
        if ($workshop) {
            $workshop->update([
                'status' => 'failed'
            ]);
        }
        $this->sendOrderConfirmationEmail($validated, $workshopId);
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
            $garageEmail = $this->getGarageEmail();
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
    private function getGarageEmail()
    {
        try {
            $garage = \App\Models\GarageDetails::first();
            if ($garage) {
                // Log::info('Garage fetched in getGarageEmail:', ['garage' => $garage->toArray()]);
            } else {
                // Log::warning('No garage found in getGarageEmail.');
            }

            return $garage ? $garage->email : null;
        } catch (\Exception $e) {
            Log::error('Error in getGarageEmail:', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }
}
