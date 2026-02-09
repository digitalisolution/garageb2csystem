<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactToAdmin extends Mailable
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
         return $this->view('emails.contact_form')
        ->with([
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'subject' => $this->data['subject'],
            'ip' => $this->data['ip'],
            'user_message' => $this->data['user_message'],
        ])
        ->subject($this->data['subject'])
        ->replyTo($this->data['email'], $this->data['name']);
    }
}
