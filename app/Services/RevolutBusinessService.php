<?php
namespace App\Services;
use App\Models\RevolutToken;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\GaragePayout;
use Exception;
use App\Models\Garage;
use App\Models\Workshop;
class RevolutBusinessService
{
    protected string $clientId;
    protected string $privateKey;
    protected string $redirectUri;
    protected string $baseUrl;
    protected bool $sandbox;
    protected string $mode;

    public function __construct()
    {
        $this->mode = get_option('revolut_business_mode', 'sandbox');
        $this->sandbox = $this->mode === 'sandbox';
        $this->clientId = get_option('revolut_business_client_id');
        $path = public_path('revolut-certificate/' . get_option('revolut_business_private_key_path'));
        if (!file_exists($path) || !is_readable($path)) {
            throw new Exception("Private key not found or not readable: {$path}");
        }
        $this->privateKey = file_get_contents($path);
        $this->redirectUri = url('/revolut/callback')   ;
        $this->baseUrl = $this->sandbox ? 'https://sandbox-b2b.revolut.com/api/1.0' : 'https://b2b.revolut.com/api/1.0';
    }
    function generateJwtAssertion(): string
    {
        $payload = [
            'iss' => parse_url($this->redirectUri, PHP_URL_HOST),
            'sub' => $this->clientId,
            'aud' => 'https://revolut.com',
            'jti' => uniqid('', true),
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        return JWT::encode($payload, $this->privateKey, 'RS256');
    }
public function getAccessToken(): string
{
    $tokenRecord = RevolutToken::where('mode', $this->mode)->first();

    // If access token still valid (with buffer)
    if ($tokenRecord && $tokenRecord->access_token_expires_at > now()->addMinutes(5)) {
        return $tokenRecord->access_token;
    }

    if (!$tokenRecord || !$tokenRecord->refresh_token) {
        throw new Exception('No refresh token. Run OAuth consent flow first.');
    }

    $jwt = $this->generateJwtAssertion();
    $response = Http::asForm()->post("{$this->baseUrl}/auth/token", [
        'grant_type' => 'refresh_token',
        'refresh_token' => $tokenRecord->refresh_token,
        'client_id' => $this->clientId,
        'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
        'client_assertion' => $jwt,
    ]);

    if ($response->failed()) {
        Log::error('Revolut token refresh failed', ['body' => $response->body()]);
        throw new Exception('Token refresh failed.');
    }

    $data = $response->json();

    RevolutToken::updateOrCreate(
        ['mode' => $this->mode],
        [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $tokenRecord->refresh_token,
            'access_token_expires_at' => now()->addMinutes(55),
            'refresh_token_expires_at' => now()->addDays(90),
        ]
    );

    return $data['access_token'];
}
    protected function getAndStoreSourceAccountId(Garage $garage): string
    {
        if ($garage->garage_revolut_source_id) {
            return $garage->garage_revolut_source_id;
        }
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->get("{$this->baseUrl}/accounts");
        if ($response->failed()) {
            Log::error('Failed to fetch Revolut accounts', ['response' => $response->body()]);
            throw new Exception('Failed to fetch accounts: ' . $response->body());
        }
        $accounts = $response->json();
        $gbpAccount = collect($accounts)->firstWhere('currency', 'GBP');
        if (!$gbpAccount) {
            throw new Exception('No GBP account found in your Revolut Business profile.');
        }
        $accountId = $gbpAccount['id'];
        // Store ONLY on the current garage
        $garage->garage_revolut_source_id = $accountId;
        $garage->save();
        Log::info('Revolut source account ID stored on specific garage', [
            'garage_id' => $garage->id,
            'account_id' => $accountId,
        ]);
        return $accountId;
    }
    protected function getSourceAccountId(Garage $garage): string
    {
        if ($garage->garage_revolut_source_id) {
            return $garage->garage_revolut_source_id;
        }
        // First time for this garage — fetch and store
        return $this->getAndStoreSourceAccountId($garage);
    }
    public function createCounterpartyForGarage(Garage $garage): string
    {
        if ($garage->garage_revoult_counterparty_id) {
            return $garage->garage_revoult_counterparty_id;
        }
        if (empty($garage->garage_bank_sort_code) || empty($garage->garage_account_number)) {
            throw new Exception('Missing sort code or account number.');
        }
        $token = $this->getAccessToken();
        $payload = [
            'profile_type' => 'business',
            'company_name' => $garage->garage_name,
            'account_no' => $garage->garage_account_number,
            'sort_code' => $garage->garage_bank_sort_code,
            'bank_country' => 'GB',
            'country' => 'GB',
            'currency' => 'GBP',
        ];
        Log::info('Creating counterparty', [
            'garage_id' => $garage->id,
            'name' => $garage->garage_name,
        ]);
        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/counterparty", $payload);
        if ($response->failed()) {
            Log::error('Counterparty creation failed', [
                'garage_id' => $garage->id,
                'response' => $response->body(),
            ]);
            throw new Exception('Counterparty creation failed: ' . $response->body());
        }
        $data = $response->json();
        $counterpartyId = $data['id'];
        $garage->garage_revoult_counterparty_id = $counterpartyId;
        $garage->save();
        Log::info('Counterparty created', [
            'garage_id' => $garage->id,
            'counterparty_id' => $counterpartyId,
        ]);
        return $counterpartyId;
    }
    public function payoutWorkshop(Workshop $workshop): array
    {
        $payoutRecord = GaragePayout::where('workshop_id', $workshop->id)
            ->where('status', 'pending')
            ->firstOrFail();
        $garage = $workshop->garage;
        if (!$garage) {
            $payoutRecord->markAsFailed('Garage not linked');
            throw new Exception('No garage associated.');
        }
        $counterpartyId = $this->createCounterpartyForGarage($garage);
        $sourceAccountId = $this->getSourceAccountId($garage);
        $payoutAmount = $payoutRecord->payout_amount;
        if ($payoutAmount <= 0) {
            $payoutRecord->markAsFailed('Invalid amount');
            throw new Exception('Payout amount invalid.');
        }
        $amountInMinor = (int) round($payoutAmount);
        $payoutRecord->update(['status' => 'processing']);
        try {
            $token = $this->getAccessToken();
            $payload = [
                'request_id' => 'payout_ws_' . $workshop->id . '_' . Str::random(8),
                'account_id' => $sourceAccountId,
                'title' => 'Garage Settlement - Workshop #' . $workshop->id,
                'receiver' => [
                    'counterparty_id' => $counterpartyId,
                ],
                'amount' => $amountInMinor,
                'currency' => 'GBP',
                'reference' => 'WS_' . $workshop->id . ' - ' . $garage->garage_name,
            ];
            Log::info('Sending payout', [
                'workshop_id' => $workshop->id,
                'payout_id' => $payoutRecord->id,
                'counterparty_id' => $counterpartyId,
                'account_id' => $sourceAccountId,
                'amount' => $payoutAmount,
            ]);
            $response = Http::withToken($token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/pay", $payload);
            if ($response->failed()) {
                $error = $response->body();
                $payoutRecord->markAsFailed("API Error: " . substr($error, 0, 500));
                throw new Exception('Payout failed: ' . $error);
            }
            $data = $response->json();
            $transactionId = $data['payments'][0]['id'] ?? 'unknown';
            $payoutRecord->markAsPaid($transactionId);
            Log::info('Payout SUCCESS', [
                'payout_id' => $payoutRecord->id,
                'workshop_id' => $workshop->id,
                'amount' => $payoutAmount,
                'tx_id' => $transactionId,
            ]);
            return $data;
        } catch (Exception $e) {
            if ($payoutRecord->status !== 'completed') {
                $payoutRecord->markAsFailed($e->getMessage());
            }
            throw $e;
        }
    }
    public function handleRefund(Workshop $workshop, float $refundAmount, string $reason = 'Customer Dispute'): array
    {
        $merchantBaseUrl = $this->sandbox ? 'https://sandbox-merchant.revolut.com' : 'https://merchant.revolut.com';
        $merchantSecret = get_option('revolut_merchant_secret');
        $amountInMinor = (int) round($refundAmount * 100);
        $response = Http::withToken($merchantSecret)
            ->withHeaders([
                'Revolut-Api-Version' => '2024-09-01',
                'Content-Type' => 'application/json',
            ])
            ->post("{$merchantBaseUrl}/orders/{$workshop->revolut_order_id}/refund", [
                'amount' => $amountInMinor,
                'description' => $reason,
            ]);
        if ($response->failed()) {
            Log::error('Customer refund failed', ['workshop_id' => $workshop->id, 'body' => $response->body()]);
            throw new Exception('Refund failed.');
        }
        $refundData = $response->json();
        if ($workshop->settled_at) {
            $clawbackAmount = ($refundAmount / $workshop->grand_total) * $workshop->settlement_amount;
            Log::warning('Clawback needed from garage', [
                'workshop_id' => $workshop->id,
                'clawback_amount' => $clawbackAmount,
            ]);
        }
        $workshop->update([
            'payment_status' => 'refunded',
            'refund_amount' => $refundAmount,
        ]);
        Log::info('Refund processed', ['workshop_id' => $workshop->id, 'amount' => $refundAmount]);
        return $refundData;
    }
}