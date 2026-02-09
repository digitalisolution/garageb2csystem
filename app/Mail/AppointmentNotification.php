<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class AppointmentNotification extends Mailable
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
        return $this->subject('New Appointment Received')
                    ->replyTo($this->appointment['email'], $this->appointment['first_name'] . ' ' . $this->appointment['last_name']) 
                    ->view('emails.appointment_notification');
    }
}
