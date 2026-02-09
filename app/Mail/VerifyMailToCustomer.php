<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Workshop;

class VerifyMailToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $garage;

    /**
     * Create a new message instance.
     */
    public function __construct(Workshop $order, $customer, $garage = null)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->garage = $garage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Job Verification Code - ' . $this->order->id)
                    ->view('emails.verify_mailto_customer')
                    ->with([
                        'order' => $this->order,
                        'customer' => $this->customer,
                        'garage' => $this->garage,
                    ]);
    }
}
