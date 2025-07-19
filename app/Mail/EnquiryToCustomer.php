<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Estimate;

class EnquiryToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;
    public $estimate;
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
        $this->estimate = Estimate::find($this->orderId);

        if ($this->estimate) {
            // Fetch workshop tyres
            $this->workshopTyres = $this->estimate->tyres;

            // Fetch workshop services
            $this->workshopServices = $this->estimate->services;

            // Fetch related bookings
            $this->bookings = $this->estimate->bookings;
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
        if (!$this->estimate) {
            Log::error('estimate not found.', ['orderId' => $this->orderId]);
            throw new \Exception('estimate not found.');
        }

        // Combine workshop tyres and services into a single collection
        $workshopProducts = collect($this->workshopTyres)->merge($this->workshopServices);

        // Prepare data for the email view
        $viewData = [
            'estimate' => $this->estimate,
            'workshopProducts' => $workshopProducts, // Combined tyres and services
            'bookings' => $this->bookings, // Include bookings in the view data
            'customer' => $this->customer,
        ];

        // Log the view data
        // Log::info('View data prepared for email.', $viewData);

        // Return the email with the view and subject
        return $this->view('emails.estimate_submitted', $viewData)
            ->subject("Estimate Submitted Successfully")
            ->replyTo($this->garage->email , $this->garage->garage_name);
    }
}