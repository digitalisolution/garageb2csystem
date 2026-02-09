<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCustomerRegisteredToGarage extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $garage;

    public function __construct($customer, $garage)
    {
        $this->customer = $customer;
        $this->garage = $garage;
    }

    public function build()
    {
        $customerName = $this->customer['name'] . ' ' . ($this->customer['last_name'] ?? '');
        
        return $this->view('emails.garage_new_customer_notification')
                    ->subject('New Customer Registration: ' . $customerName)
                    ->replyTo($this->customer['email'], $customerName);
    }
}