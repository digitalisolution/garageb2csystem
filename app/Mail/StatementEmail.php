<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $pdfPath = null)
    {
        $this->data = $data;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->view('emails.statement')
            ->subject('Customer Statement');

        if ($this->pdfPath) {
            $mail->attach($this->pdfPath, [
                'as' => 'statement.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}