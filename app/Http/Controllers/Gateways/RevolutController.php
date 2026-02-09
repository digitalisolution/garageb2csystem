<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workshop;
use Illuminate\Support\Facades\Log;
use App\Services\RevolutService;

class RevolutController extends Controller
{
    public function makePayment($workshopId, RevolutService $revolut)
    {
        $workshop = Workshop::findOrFail($workshopId);

        $payload = [
            'amount' => intval($workshop->grandTotal * 100),
            'currency' => 'GBP',
            'merchant_order_ext_ref' => (string) $workshop->id,
            'capture_mode' => 'AUTOMATIC',
            'merchant_customer_ext_ref' => (string) $workshop->customer_id,
            'return_url' => route('revolut.success', $workshop->id),
            'cancel_url' => route('revolut.cancel', $workshop->id),
        ];

        $response = $revolut->createOrder($payload);
        $workshop->update([
            'revolut_order_id' => $response['id'],
            'payment_status' => 'pending',
        ]);
        return redirect($response['public_url']);
    }

    public function paymentSuccess($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->update(['payment_status' => 'paid']);
        return view('payments.success', compact('workshop'));
    }

    public function paymentCancel($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->update(['payment_status' => 'cancelled']);
        return view('payments.cancel', compact('workshop'));
    }

public function webhook(Request $request, RevolutService $service)
{
    Log::info('🔥 Revolut webhook HIT', [
        'headers' => collect($request->headers->all())
            ->map(fn ($v) => implode(',', $v))
            ->toArray(),
        'raw_body' => $request->getContent(),
    ]);

    $signatureHeader = $request->header('Revolut-Signature');
    $timestamp = $request->header('Revolut-Request-Timestamp');

    if (!$signatureHeader || !$timestamp) {
        return response('Missing headers', 400);
    }

    $signatures = array_filter(explode(',', $signatureHeader), fn($s) => str_starts_with($s, 'v1='));
    if (empty($signatures)) {
        return response('Invalid signature format', 400);
    }

    $rawBody = $request->getContent();
    $signedPayload = 'v1.' . $timestamp . '.' . $rawBody;

    $secret = get_option('revolut_merchant_webhook_secret');
    $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);
    $valid = false;
    foreach ($signatures as $sig) {
        $received = substr($sig, 3);
        if (hash_equals($expectedSignature, $received)) {
            $valid = true;
            break;
        }
    }

    if (!$valid) {
        Log::error('❌ Revolut signature mismatch', [
            'expected' => $expectedSignature,
            'received_options' => array_map(fn($s) => substr($s, 3), $signatures),
            'signed_payload' => $signedPayload,
        ]);
        return response('Invalid signature', 401);
    }

    Log::info('✅ Revolut webhook VERIFIED');

    $data = json_decode($rawBody, true);

    $event = $data['event'] ?? null;

    if ($event === 'ORDER_COMPLETED') {
        $workshopId = str_replace('WS_', '', $data['merchant_order_ext_ref'] ?? '');
        $orderId = $data['order_id'] ?? '';

        $service->addPaymentWebsite([
            'workshopid' => $workshopId,
            'transactionid' => $orderId,
            'status' => 'paid',
        ]);

        Log::info('💰 ORDER_COMPLETED processed', ['workshop_id' => $workshopId]);
    }

    if (in_array($event, ['ORDER_PAYMENT_FAILED', 'ORDER_PAYMENT_DECLINED', 'ORDER_FAILED'])) {
        $workshopId = str_replace('WS_', '', $data['merchant_order_ext_ref'] ?? '');
        $service->addPaymentWebsite([
            'workshopid' => $workshopId,
            'status' => 'failed',
        ]);
    }

    return response()->json(['ok' => true]);
}

public function checkRevolutStatus($id, RevolutService $revolut)
{
    $workshop = Workshop::findOrFail($id);

    if (!$workshop->revolut_order_id) {
        return response()->json(['state' => null]);
    }

    $order = $revolut->getOrder($workshop->revolut_order_id);

    return response()->json([
        'state' => $order['state'] ?? null,
    ]);
}

}
