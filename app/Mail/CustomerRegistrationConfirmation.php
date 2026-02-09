<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerRegistrationConfirmation extends Mailable
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
        return $this->view('emails.customer_registration_confirmation')
                    ->subject('Welcome to ' . ($this->garage->name ?? 'Our Garage Service'))
                    ->replyTo($this->garage->email ?? env('MAIL_FROM_ADDRESS'), $this->garage->name ?? 'Garage Solutions');
    }
}