<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $customerEmail;

    /**
     * Create a new message instance.
     *
     * @param string $password The generated password.
     * @param string $customerEmail The customer's email address.
     */
    public function __construct($password, $customerEmail)
    {
        $this->password = $password;
        $this->customerEmail = $customerEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Account Password')
            ->view('emails.customer_password');
    }
}