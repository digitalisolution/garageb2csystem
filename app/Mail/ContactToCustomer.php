<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $garage;

    public function __construct($data, $garage)
    {
        $this->data = $data;
        $this->garage = $garage;
    }

    public function build()
    {
        return $this->view('emails.customer_confirmation')
         ->with([
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'subject' => $this->data['subject'],
            'ip' => $this->data['ip'],
            'user_message' => $this->data['user_message'],
        ])
            ->subject('Thank you for contacting us!')
            ->replyTo($this->garage->email, $this->garage->garage_name ?? 'Garage');
    }
}
