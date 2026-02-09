<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MobilefittingformConfirmation extends Mailable
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
        return $this->subject('Mobilefittingform Confirmation')->view('emails.mobilefittingform_confirmation');
    }
}
