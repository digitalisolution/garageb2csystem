<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class MobilefittingformNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mobilefittingform;

    public $garage;

    public function __construct($mobilefittingform ,$garage)
    {
        $this->mobilefittingform = $mobilefittingform;
        $this->garage = $garage;
    }

    public function build()
    {
        return $this->subject('New Mobilefittingform Received')
                    ->replyTo($this->mobilefittingform['email'], $this->mobilefittingform['first_name']) 
                    ->view('emails.mobilefittingform_notification');
    }
}
