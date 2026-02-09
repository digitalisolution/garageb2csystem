<?php
namespace App\Http\Controllers\Gateways;
use App\Http\Controllers\Controller;
use App\Models\RevolutToken;
use Illuminate\Http\Request;
use App\Services\RevolutBusinessService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use App\Models\GaragePayout;
use Illuminate\Support\Facades\Log;
use App\Models\Workshop;
use Exception;
class RevolutBusinessController extends Controller
{

    public function oauthRedirect()
    {
        $mode = get_option('revolut_business_mode', 'sandbox');
        $consentBase = ($mode === 'sandbox') ? 'https://sandbox-business.revolut.com' : 'https://business.revolut.com';
        $url = $consentBase . '/app-confirm?' . http_build_query([
            'client_id' => get_option('revolut_business_client_id'),
            'redirect_uri' => url('/revolut/callback'),
            'response_type' => 'code',
        ]);
        return redirect($url);
    }

    public function oauthCallback(Request $request, RevolutBusinessService $service)
    {
        $code = $request->query('code');
        if (!$code) {
            return response('Error: No authorization code received.', 400);
        }

        $mode = get_option('revolut_business_mode', 'sandbox');
        $tokenBase = ($mode === 'sandbox')
            ? 'https://sandbox-b2b.revolut.com/api/1.0'
            : 'https://b2b.revolut.com/api/1.0';

        try {
            $jwt = $service->generateJwtAssertion();

            $response = Http::asForm()->post($tokenBase . '/auth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => get_option('revolut_business_client_id'),
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('Revolut token exchange failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'mode' => $mode,
                ]);

                return response('Token exchange failed: ' . $response->body(), 500);
            }

            $tokens = $response->json();

            if (!isset($tokens['access_token']) || !isset($tokens['refresh_token'])) {
                Log::error('Invalid token response from Revolut', ['response' => $tokens]);
                return response('Invalid response from Revolut: missing tokens.', 500);
            }

            RevolutToken::updateOrCreate(
                ['mode' => $mode],
                [
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'],
                    'access_token_expires_at' => now()->addMinutes(55),
                    'refresh_token_expires_at' => now()->addDays(90),
                ]
            );

            $envName = ucfirst($mode);

            return response('
            <h1 style="color:green;">Success!</h1>
            <p>Revolut Business <strong>' . $envName . '</strong> authorized successfully.</p>
            <p>Access & Refresh tokens have been securely stored in the database.</p>
            <p>You can now perform payouts and other API operations.</p>
            <br>
            <a href="/AutoCare/payouts" style="padding:10px 20px; background:#00d924; color:white; text-decoration:none; border-radius:5px;">Go to Payouts Dashboard</a>
        ');

        } catch (Exception $e) {
            Log::error('Revolut OAuth callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mode' => $mode,
            ]);

            return response('Error during authorization: ' . $e->getMessage(), 500);
        }
    }

    public function payout(Workshop $workshop, RevolutBusinessService $service)
{
        // dd('test');
    $payout = GaragePayout::where('workshop_id', $workshop->id)
        ->where('status', 'pending')
        ->firstOrFail();

    try {
        $result = $service->payoutWorkshop($workshop);

        // $this->generatePayoutInvoice($payout, $result);

        return back()->with('success', 'Payout sent successfully! Invoice generated.');
    } catch (Exception $e) {
        Log::error('Manual payout failed', [
            'workshop_id' => $workshop->id,
            'error' => $e->getMessage()
        ]);
        return back()->with('error', 'Payout failed: ' . $e->getMessage());
    }
}

    public function refund(Request $request, Workshop $workshop, RevolutBusinessService $service)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'string|nullable',
        ]);
        try {
            $result = $service->handleRefund($workshop, $validated['amount'], $validated['reason'] ?? 'Customer Dispute');
            return response()->json(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function bulkPayout(Request $request, RevolutBusinessService $service)
    {
        $payoutIds = $request->input('payout_ids', []);
        if (empty($payoutIds)) {
            return back()->with('error', 'No payouts selected.');
        }

        $successCount = 0;
        $failed = [];

        foreach ($payoutIds as $id) {
            $payout = GaragePayout::find($id);
            if (!$payout || $payout->status !== 'pending')
                continue;

            try {
                $workshop = $payout->workshop;
                $result = $service->payoutWorkshop($workshop);
                $this->generatePayoutInvoice($payout, $result);

                $successCount++;
            } catch (Exception $e) {
                $failed[] = "#{$payout->workshop->id} - " . $e->getMessage();
                Log::error('Bulk payout failed', ['payout_id' => $id, 'error' => $e->getMessage()]);
            }
        }

        $message = "Bulk payout completed: {$successCount} successful.";
        if (!empty($failed)) {
            $message .= " Failed: " . implode(', ', array_slice($failed, 0, 5));
        }

        return back()->with('success', $message);
    }

private function generatePayoutInvoice(GaragePayout $payout, array $result)
{
    $transactionId = $result['payments'][0]['id'] ?? 'N/A';

    $pdf = Pdf::loadView('invoices.garage-payout', [
        'payout' => $payout,
        'transactionId' => $transactionId,
        'date' => now()->format('d M Y'),
    ]);

    $filename = 'payout-invoice-' . $payout->id . '-' . now()->format('Ymd-His') . '.pdf';
    $directory = 'invoices/payouts';
    $fullPath = $directory . '/' . $filename;

    // Save to public disk so asset() can access it
    Storage::disk('public')->put($fullPath, $pdf->output());

    // Save path relative to storage/app/public → accessible via asset('storage/...')
    $payout->update([
        'transaction_id' => $transactionId,
        'invoice_path' => $fullPath, // e.g. invoices/payouts/xxx.pdf
    ]);
}
}