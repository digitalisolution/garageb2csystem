<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $garage;

    /**
     * Create a new message instance.
     *
     * @param string $resetUrl
     * @param object $garage
     */
    public function __construct($resetUrl, $garage)
    {
        $this->resetUrl = $resetUrl;
        $this->garage = $garage; // Pass the garage details (e.g., garage name)
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->garage->garage_email, 'Digital Solutions')
            ->view('emails.customer_reset_password') // Path to your Blade template
            ->subject(getGarageDetails()->garage_name . ' - Password Reset Request');
    }
}