<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\GaragePayoutInvoiceMail;
use App\Models\GaragePayout;
use App\Models\Garage;
use App\Models\GaragePayoutInvoice;
use App\Services\GaragePayoutInvoiceService;
use App\Services\RevolutBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GaragePayoutController extends Controller
{
    public function index(Request $request)
    {
        $garages = Garage::all();

        $query = GaragePayout::with(['garage', 'workshop'])
            ->whereHas('workshop', function ($q) {
                $q->where('is_converted_to_invoice', 1)
                ->where('is_void', 0)
                ->where('status', 'completed');
            });

        // Garage filter
        if ($request->filled('garage_id')) {
            $query->where('garage_id', $request->garage_id);
            $selectedGarage = Garage::find($request->garage_id);
        } else {
            $selectedGarage = null;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('workshop', function ($q2) use ($search) {
                    $q2->where('id', 'like', "%{$search}%");
                })
                ->orWhereHas('garage', function ($q2) use ($search) {
                    $q2->where('garage_name', 'like', "%{$search}%");
                });
            });
        }

        $query->orderByDesc('created_at');

        $payouts = $query->paginate(20);
        $payouts->load('payoutInvoice');
        $payouts->getCollection()->transform(function ($payout) {
            $payout->tyres_count = $payout->workshop->items()->count() ?? 0;
            return $payout;
        });

        return view('AutoCare.payouts.index', compact(
            'garages',
            'selectedGarage',
            'payouts'
        ));
    }

    public function payout($payoutId, RevolutBusinessService $revolutService)
    {
        $payout = GaragePayout::with('workshop.garage')->findOrFail($payoutId);

        if (!$payout->isPending()) {
            return back()->with('error', 'This payout is not pending.');
        }

        try {
            $revolutService->payoutWorkshop($payout->workshop);
            return back()->with('success', "Payout of £{$payout->payout_amount} sent to {$payout->garage->name}!");
        } catch (\Exception $e) {
            Log::error('Manual payout failed', ['payout_id' => $payoutId, 'error' => $e->getMessage()]);
            return back()->with('error', 'Payout failed: ' . $e->getMessage());
        }
    }

    public function bulkPayout(Request $request, RevolutBusinessService $revolutService)
    {
        $payoutIds = $request->input('payouts', []);

        if (empty($payoutIds)) {
            return back()->with('error', 'No payouts selected.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($payoutIds as $payoutId) {
            $payout = GaragePayout::find($payoutId);

            if (!$payout || !$payout->isPending()) {
                $failCount++;
                continue;
            }

            try {
                $revolutService->payoutWorkshop($payout->workshop);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                Log::error('Bulk payout failed', ['payout_id' => $payoutId, 'error' => $e->getMessage()]);
            }
        }

        $message = "Bulk payout complete: {$successCount} successful";
        if ($failCount > 0) {
            $message .= ", {$failCount} failed";
        }

        return back()->with('success', $message);
    }

public function view(GaragePayoutInvoice $invoice)
{
    // Eager load all related data for the view
    $invoice->load([
        'garagePayout.garage',
        'garagePayout.workshop',
        'garagePayout.workshop.items',
        'garagePayout.workshop.services',
        'garagePayout.workshop.tyres'
    ]);

    // Optional: Authorization (uncomment if using policies)
    // if (!auth()->user()->can('view-invoice', $invoice)) {
    //     abort(403);
    // }

    // Prepare dynamic data for the view
    $payout = $invoice->garagePayout;
    $garage = $payout->garage;
    $workshop = $payout->workshop;

    $data = [
        'invoice' => $invoice,
        'payout' => $payout,
        'garage' => $garage,
        'workshop' => $workshop,
        'company' => [
            'name' => get_option('company_name', config('app.name')),
            'address' => get_option('company_address', ''),
            'email' => get_option('company_email', ''),
            'phone' => get_option('company_phone', ''),
            'vat_number' => get_option('company_vat_number', ''),
            'registration_number' => get_option('company_registration_number', ''),
        ],
        'issueDate' => $invoice->issue_date?->format('d F Y') ?? now()->format('d F Y'),
        'dueDate' => $invoice->due_date?->format('d F Y') ?? now()->addDays(30)->format('d F Y'),
    ];

    return view('AutoCare.payouts.payout-invoice', $data);
}

    public function download(GaragePayoutInvoice $invoice, GaragePayoutInvoiceService $service)
    {
        // if (!auth()->user()->can('view-invoice', $invoice)) {
        //     abort(403);
        // }

        return $service->download($invoice);
    }

   public function send(GaragePayoutInvoice $invoice, GaragePayoutInvoiceService $service, Request $request)
{
    // Authorization check
    // if (!auth()->user()->can('send-invoice', $invoice)) {
    //     if ($request->expectsJson()) {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    //     }
    //     abort(403);
    // }

    $garage = $invoice->garagePayout->garage;

    if (empty($garage->garage_email)) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Garage has no email address'], 400);
        }
        return back()->with('error', 'Garage has no email address.');
    }

    try {
        Mail::to($garage->garage_email)->send(new GaragePayoutInvoiceMail($invoice));
        
        $service->markAsSent($invoice);
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => "Invoice #{$invoice->invoice_number} sent to {$garage->garage_email}"
            ]);
        }

        return back()->with('success', "Invoice #{$invoice->invoice_number} sent to {$garage->garage_email}");
        
    } catch (\Exception $e) {
        Log::error('Invoice send failed', [
            'invoice_id' => $invoice->id, 
            'error' => $e->getMessage()
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send: ' . $e->getMessage()
            ], 500);
        }
        
        return back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
    }
}


}