<?php

namespace App\Services;

use App\Models\GaragePayout;
use Illuminate\Support\Facades\Http;
use App\Models\Workshop;
use App\Models\PaymentRecord;
use App\Models\CustomerDebitLog;
use App\Models\PaymentHistory;
use App\Services\ApiOrderingService;
use App\Services\UpdateOrderQtyService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;

class RevolutService
{
    protected string $baseUrl;
    protected string $secret;
    protected ApiOrderingService $apiOrderingService;
    protected UpdateOrderQtyService $updateOrderQtyService;

    public function __construct(
        ApiOrderingService $apiOrderingService,
        UpdateOrderQtyService $updateOrderQtyService
    ) {
        $mode = get_option('revolut_merchant_mode');
        $this->sandbox = ($mode === 'sandbox');
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox-merchant.revolut.com/api/1.0'
            : 'https://merchant.revolut.com/api/1.0';



        $this->secret = get_option('revolut_merchant_secret');
        $this->apiOrderingService = $apiOrderingService;
        $this->updateOrderQtyService = $updateOrderQtyService;
    }

    /**
     * STEP 1: Redirect customer to Revolut
     */
    public function processPaymentWebsite(array $data)
    {
        $workshop = Workshop::findOrFail($data['workshop_id']);

        $payload = [
        'amount' => (int) round($workshop->grandTotal * 100),
        'currency' => 'GBP',
        'return_url' => route('revolut.return'),
        'merchant_order_ext_ref' => 'WS_' . $workshop->id,
        ];

        $response = Http::withToken($this->secret)
            ->withHeaders([
                'Revolut-Api-Version' => '2024-09-01',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/orders', $payload);

        if ($response->failed()) {
            Log::error('Revolut order create failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Revolut payment failed');
        }

        $order = $response->json();
        // dd($response);
        return redirect()->away($order['checkout_url']);
    }

    protected function updateRecordWebsite($workshopId, $paymentRecordId, $amount)
    {
        Workshop::where('id', $workshopId)->update([
            'payment_status' => 1,
            'paid_price' => $amount,
            'balance_price' => 0,
            'status' => 'booked',
        ]);
    }
    
    protected function isPaymentAlreadyProcessed(int $workshopId, string $revolutOrderId): bool
    {
        return PaymentRecord::where('workshop_id', $workshopId)
            ->where('transactionid', $revolutOrderId)
            ->exists();
    }

    /**
     * STEP 2: FINALISE PAYMENT (Webhook ONLY)
     */
    public function addPaymentWebsite(array $data)
    {
        return DB::transaction(function () use ($data) {
            $workshop = Workshop::find($data['workshopid']);
            if (!$workshop) {
                Log::warning('Revolut: Workshop not found', ['workshop_id' => $data['workshopid']]);
                return ['status' => 'error', 'message' => 'Workshop not found'];
            }

            $revolutOrderId = $data['transactionid'] ?? 'unknown';
            if ($data['status'] === 'paid' && $this->isPaymentAlreadyProcessed($workshop->id, $revolutOrderId)) {
            return ['status' => 'duplicate'];
            }

            if ($data['status'] === 'paid') {
                $paymentRecord = PaymentRecord::create([
                    'workshop_id' => $workshop->id,
                    'amount' => $workshop->grandTotal,
                    'paymentmode' => 'revolut',
                    'transactionid' => $revolutOrderId,
                    'date' => Carbon::now('Europe/London')->format('Y-m-d'),
                    'daterecorded' => Carbon::now('Europe/London')->format('Y-m-d H:i:s'),
                ]);

                $paymentHistory = PaymentHistory::create([
                    'job_id' => $workshop->id,
                    'payment_date' => Carbon::now('Europe/London')->format('Y-m-d'),
                    'payment_amount' => $workshop->grandTotal,
                ]);

                CustomerDebitLog::create([
                    'customer_id' => $workshop->customer_id,
                    'workshop_id' => $workshop->id,
                    'payment_history_id' => $paymentHistory->id,
                    'created_at' => Carbon::now('Europe/London'),
                    'debit_amount' => $workshop->grandTotal,
                    'is_debit' => 1,
                    'comments' => 'Online Payment via Revolut (Webhook)',
                    'payment_type' => 2,
                ]);

                $this->updateRecordWebsite($workshop->id, $paymentRecord->id, $workshop->grandTotal);
                $this->apiOrderingService->processApiOrder($workshop->id);
                $this->updateOrderQtyService->updateStockQty($workshop->id);

                $this->sendOrderConfirmationEmail([
                    'customer_name' => $workshop->name,
                    'email' => $workshop->email,
                ], $workshop->id);

                $this->createGaragePayoutRecord($workshop,$revolutOrderId);

                Session::flush();

                return [
                    'payment_record_id' => $paymentRecord->id,
                    'payment_status' => 'success',
                ];

            } elseif ($data['status'] === 'failed') {
                Workshop::where('id', $workshop->id)->update([
                    'payment_status' => 0,
                    'status' => 'payment_failed',
                ]);

                Log::warning('❌ Revolut payment failed', [
                    'workshop_id' => $workshop->id,
                    'revolut_order_id' => $revolutOrderId,
                    'revolut_data' => $data['revolut_data'] ?? [],
                ]);

                return [
                    'payment_status' => 'failed',
                ];
            }

            return ['status' => 'ignored'];
        });
    }

//    protected function createGaragePayoutRecord(Workshop $workshop, string $revolutOrderId)
// {
//     $grandTotal = $workshop->grandTotal ?? 0;
//     if ($grandTotal <= 0) {
//         Log::warning('Workshop has no valid grandTotal - payout skipped', [
//             'workshop_id' => $workshop->id,
//             'grandTotal' => $grandTotal,
//         ]);
//         return;
//     }

//     if (!$workshop->relationLoaded('garage')) {
//         $workshop->load('garage');
//     }

//     $garage = $workshop->garage;

//     if (!$garage) {
//         Log::warning('No garage linked to workshop - payout skipped', [
//             'workshop_id' => $workshop->id,
//         ]);
//         return;
//     }

//     $cardFeePercentage = (float) $garage->card_processing_fee ?? 0;

//     if ($cardFeePercentage < 0 || $cardFeePercentage > 100) {
//         Log::warning('Invalid card processing fee percentage - using 0%', [
//             'garage_id' => $garage->id,
//             'card_processing_fee' => $garage->card_processing_fee,
//         ]);
//         $cardFeePercentage = 0;
//     }

//     $cardProcessingFee = round(($grandTotal * ($cardFeePercentage / 100)), 2);
//     try {
//         $platformCommission = $garage->getCommissionAmount($workshop);
//     } catch (\Exception $e) {
//         Log::error('Failed to calculate commission', [
//             'garage_id' => $garage->id,
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//         ]);
//         $platformCommission = 0;
//     }

//     $payoutAmount = $platformCommission;
//         $commission = $grandTotal - $platformCommission - $cardProcessingFee;

//     if ($payoutAmount < 0) {
//         Log::warning('Payout amount negative - set to zero', [
//             'workshop_id' => $workshop->id,
//             'grand_total' => $grandTotal,
//             'commission' => $commission,
//             'card_fee' => $cardProcessingFee,
//             'calculated_payout' => $payoutAmount,
//         ]);
//         $payoutAmount = 0;
//     }

//     $payoutAmount = round($payoutAmount, 2);


//     // Step 6: Create Payout Record
//     try {
//         $payoutRecord = GaragePayout::create([
//             'garage_id' => $garage->id,
//             'workshop_id' => $workshop->id,
//             'customer_paid_amount' => $grandTotal,
//             'platform_commission' => round($commission, 2),
//             'card_processing_fee' => $cardProcessingFee,
//             'payout_amount' => $payoutAmount,
//             'status' => 'pending',
//             'revolut_transaction_id' => $revolutOrderId,
//             'paid_at' => null,
//         ]);
//     } catch (\Exception $e) {
//         Log::error('❌ Failed to create GaragePayout record', [
//             'workshop_id' => $workshop->id,
//             'garage_id' => $garage->id,
//             'error' => $e->getMessage(),
//             'trace' => $e->getTraceAsString(),
//         ]);
//         // Optionally re-throw or handle
//         throw $e;
//     }
// }

protected function createGaragePayoutRecord(Workshop $workshop, string $revolutOrderId)
{
   
    $grandTotal = $workshop->grandTotal ?? 0;

  
    if ($grandTotal <= 0) {
        Log::warning('❌ Grand total is zero or negative. Skipping payout.');
        return;
    }

    $workshop->loadMissing(['garage', 'items', 'services.service']);


    $garage = $workshop->garage;

    if (!$garage) {
        Log::error('❌ No garage linked to workshop');
        return;
    }

    $totalTyreCommission = 0;
    $totalServiceCommission = 0;

    foreach ($workshop->items as $tyre) {

        $fittingPrice   = $tyre->garage_fitting_charges ?? 0;
        $commissionRate = $garage->commission_price ?? 0;

        if ($garage->commission_type === 'Percentage') {

            $commissionAmount = $fittingPrice * ($commissionRate / 100);
            $garagePayout = $fittingPrice - $commissionAmount;

        } else {

            $garagePayout = $fittingPrice - $commissionRate;
        }

        $totalTyreCommission += $garagePayout;
    }

    foreach ($workshop->services as $serviceItem) {

        if (!$serviceItem->service) {
            Log::warning('Service relation missing', [
                'service_item_id' => $serviceItem->id
            ]);
            continue;
        }

        $service = $serviceItem->service;

        if ($service->service_commission_price) {
            $totalServiceCommission += $service->service_commission_price;
        }
    }

    $totalCommission = round($totalTyreCommission + $totalServiceCommission, 2);
    $cardFeePercentage = (float) ($garage->card_processing_fee ?? 0);
    $cardProcessingFee = round(($grandTotal * ($cardFeePercentage / 100)), 2);
    $payoutAmount = round($totalCommission - $cardProcessingFee, 2);

    if ($payoutAmount < 0) {
        Log::warning('⚠ Payout negative, setting to zero');
        $payoutAmount = 0;
    }

    GaragePayout::create([
        'garage_id' => $garage->id,
        'workshop_id' => $workshop->id,
        'customer_paid_amount' => $grandTotal,
        'platform_commission' => $totalCommission,
        'card_processing_fee' => $cardProcessingFee,
        'payout_amount' => $payoutAmount,
        'status' => 'pending',
        'revolut_transaction_id' => $revolutOrderId,
        'paid_at' => null,
    ]);
}

    protected function sendOrderConfirmationEmail(array $validated, $orderId)
    {
        $garage = \App\Models\GarageDetails::first();

        Mail::to($validated['email'])->send(
            new SendMailToCustomer($orderId, $validated, $garage)
        );

        Mail::to('info@digitalideasltd.co.uk')->send(
            new OrderToGarage($orderId, $validated, $garage)
        );
    }
}
