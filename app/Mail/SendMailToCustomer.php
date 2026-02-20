<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Workshop;

class SendMailToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;
    public $workshop;
    public $workshopTyres;
    public $workshopServices;
    public $customer;
    public $garage;
    public $bookings; // Add this property

    /**
     * Constructor
     *
     * @param int $orderId
     * @param array $customer
     */
    public function __construct($orderId, $customer, $garage)
    {
        $this->orderId = $orderId;
        $this->customer = $customer;
        $this->garage = $garage;

        // Fetch the workshop details based on the order ID
        $this->workshop = Workshop::find($this->orderId);

        if ($this->workshop) {
            // Fetch workshop tyres
            $this->workshopTyres = $this->workshop->tyres;

            // Fetch workshop services
            $this->workshopServices = $this->workshop->services;

            // Fetch related bookings
            $this->bookings = $this->workshop->bookings;
        } else {
            $this->workshopTyres = [];
            $this->workshopServices = [];
            $this->bookings = [];
        }

        // Log fetched data for debugging
        // Log::info('Workshop, workshop tyres, workshop services, and bookings fetched.', [
        //     'workshop' => $this->workshop,
        //     'workshopTyres' => $this->workshopTyres,
        //     'workshopServices' => $this->workshopServices,
        //     'bookings' => $this->bookings,
        // ]);
    }

    /**
     * Build the email
     *
     * @return $this
     */
    public function build()
    {
        if (!$this->workshop) {
            Log::error('Workshop not found.', ['orderId' => $this->orderId]);
            throw new \Exception('Workshop not found.');
        }
        $workshopProducts = collect($this->workshopTyres)->merge($this->workshopServices);
        $viewData = [
            'workshop' => $this->workshop,
            'workshopProducts' => $workshopProducts,
            'bookings' => $this->bookings,
            'customer' => $this->customer,
        ];

        $status = $this->workshop->status ?? 'pending';
        $subject = match (strtolower($status)) {
            'pending'     => 'Your Booking Request Has Been Pending',
            'booked'   => 'Your Order Submitted Successfully',
            'processing' => 'We’re Working on Your Order',
            'completed'   => 'Your Order is Complete – Thank You!',
            'cancelled'   => 'Your Booking Has Been Cancelled',
            'failed'    => 'Update: Your Booking Request',
            default       => 'Update Regarding Your Order #' . $this->orderId,
        };

        // Return the email with the view and subject
        return $this->view('emails.order_submitted', $viewData)
            ->subject($subject)
            ->replyTo($this->garage->email , $this->garage->garage_name);
    }
}