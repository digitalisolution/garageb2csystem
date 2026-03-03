<?php

namespace App\Mail;

use App\Models\GaragePayoutInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GaragePayoutInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(GaragePayoutInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $garage = $this->invoice->garagePayout->garage;
        
        return $this->subject("Payout Invoice #{$this->invoice->invoice_number}")
            ->view('emails.garage-payout-invoice')
            ->attachFromStorageDisk('public', $this->invoice->pdf_path)
            ->with([
                'garageName' => $garage->garage_name,
                'invoiceNumber' => $this->invoice->invoice_number,
                'amount' => $this->invoice->amount,
                'date' => $this->invoice->issue_date->format('d M Y'),
            ]);
    }
}