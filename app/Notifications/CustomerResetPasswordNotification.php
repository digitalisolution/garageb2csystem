<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CustomerResetPasswordMail;

use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPasswordNotification extends Notification
{
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            // Generate the reset URL
            $resetUrl = url(route('customer.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            // Log the generated URL
            $garage_name = getGarageDetails()->garage_name;
            $garage_email = getGarageDetails()->garage_email;
            // dd($garagename);
            // \Log::info('Generated reset URL:', $garagename);

            // Get the garage details (replace with your logic to fetch the garage)
            $garage = (object) ['garage_name' => $garage_name, 'garage_email' => $garage_email]; // Example garage name

            // Send the email using the Mailable class
            Mail::to($notifiable->getEmailForPasswordReset())->send(new CustomerResetPasswordMail($resetUrl, $garage));
        } catch (\Exception $e) {
            \Log::error('Error sending password reset email:', ['message' => $e->getMessage()]);
        }

        return null; // Return null since the email is sent manually
    }
}