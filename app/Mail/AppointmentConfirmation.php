<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $garage;

    public function __construct($appointment ,$garage)
    {
        $this->appointment = $appointment;
        $this->garage = $garage;
    }

    public function build()
    {
        return $this->subject('Appointment Confirmation')
                    ->replyTo($this->garage->email, $this->garage->garage_name) 
                    ->view('emails.appointment_confirmation');
    }
}
