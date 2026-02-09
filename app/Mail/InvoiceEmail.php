<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $emailBody;
    public $pdfPath;

    public function __construct($invoice, $emailBody, $pdfPath = null)
    {
        $this->invoice = $invoice;
        $this->emailBody = $emailBody;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $email = $this->subject('Invoice Details')
            ->view('emails.invoice-email')
            ->with(['emailBody' => $this->emailBody]);

        if ($this->pdfPath) {
            $email->attach($this->pdfPath);
        }

        return $email;
    }
}