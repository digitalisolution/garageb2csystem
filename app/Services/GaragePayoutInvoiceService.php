<?php

namespace App\Services;

use App\Models\GaragePayout;
use App\Models\GaragePayoutInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class GaragePayoutInvoiceService
{
    protected string $invoicePrefix = 'GPI';

    public function createInvoice(GaragePayout $payout, string $transactionId): GaragePayoutInvoice
    {
        if ($payout->invoice) {
            return $payout->invoice;
        }

        $invoiceNumber = $this->generateInvoiceNumber();
        $garage = $payout->garage;

        $invoice = GaragePayoutInvoice::create([
            'garage_payout_id' => $payout->id,
            'invoice_number' => $invoiceNumber,
            'revolut_transaction_id' => $transactionId !== 'unknown' ? $transactionId : null,
            'amount' => $payout->payout_amount,
            'currency' => 'GBP',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => 'issued',
            'metadata' => [
                'workshop_id' => $payout->workshop_id,
                'garage_name' => $garage->garage_name,
                'generated_by' => auth()->id() ?? 'system',
            ],
        ]);

        // Generate & store PDF
        $this->generateAndStorePdf($invoice, $payout, $transactionId);

        return $invoice;
    }

    /**
     * Generate PDF and save to storage
     */
    protected function generateAndStorePdf(GaragePayoutInvoice $invoice, GaragePayout $payout, string $transactionId): void
    {
        $data = [
            'invoice' => $invoice,
            'payout' => $payout,
            'garage' => $payout->garage,
            'workshop' => $payout->workshop,
            'transactionId' => $transactionId,
            'date' => $invoice->issue_date->format('d F Y'),
            'company' => [
                'name' => config('app.name', 'Your Company'),
                'address' => get_option('company_address', ''),
                'email' => get_option('company_email', ''),
                'phone' => get_option('company_phone', ''),
            ],
        ];

        $pdf = PDF::loadView('AutoCare.payouts.payout-invoice-pdf', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        // Save PDF
        $filename = 'invoices/garage-payouts/' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $invoice->update(['pdf_path' => $filename]);
    }

    /**
     * Generate sequential invoice number: GPI-2024-00123
     */
    protected function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $last = GaragePayoutInvoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        $nextNumber = $last 
            ? (int)Str::afterLast($last, '-') + 1 
            : 1;

        return $this->invoicePrefix . '-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Download invoice PDF
     */
    public function download(GaragePayoutInvoice $invoice)
    {
        if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
            // Regenerate if missing
            $payout = $invoice->garagePayout;
            $this->generateAndStorePdf($invoice, $payout, $invoice->revolut_transaction_id ?? 'unknown');
        }

        return Storage::disk('public')->download(
            $invoice->pdf_path, 
            $invoice->invoice_number . '.pdf'
        );
    }

    /**
     * Mark invoice as sent (email delivered)
     */
    public function markAsSent(GaragePayoutInvoice $invoice): void
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Void an invoice (e.g., payout reversed)
     */
    public function void(GaragePayoutInvoice $invoice, string $reason): void
    {
        if ($invoice->isVoid()) {
            throw new Exception('Invoice already voided');
        }

        $invoice->update([
            'status' => 'void',
            'notes' => trim(($invoice->notes ?? '') . "\n[Voided: {$reason}]"),
        ]);
    }
}