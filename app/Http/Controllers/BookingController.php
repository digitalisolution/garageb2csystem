<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\WorkshopTyre;
use App\Models\WorkshopService;
use App\Models\Customer;
use App\Models\RegionCounty;
use App\Models\Countries;
use App\Models\VehicleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Import Str helper for string manipulation
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    // Fetch all bookings for a specific workshop
    public function getBookings()
    {
        try {
            $bookings = Booking::all(['id', 'workshop_id', 'start', 'end', 'title']);
            return response()->json($bookings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Save a new booking
    public function saveBooking(Request $request)
    {
        $validated = $request->validate([
            'workshop_id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $booking = Booking::create([
            'workshop_id' => $validated['workshop_id'],
            'title' => 'Booked Slot', // Default title for a booking
            'start' => Carbon::parse($validated['start']),
            'end' => Carbon::parse($validated['end']),
        ]);

        return response()->json(['success' => true, 'message' => 'Booking saved!', 'booking' => $booking]);
    }

    // Create a job for a specific booking
    public function createJob(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Simulate job creation logic
        return response()->json(['success' => true, 'message' => 'Job created for booking ID ' . $id]);
    }


    public function getBookingDetails($id)
    {
        // Fetch booking with workshop details
        $booking = Booking::with(['workshop'])
            ->where('id', $id)
            ->first();
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $workshop = $booking->workshop;

        // Format due_in and due_out using Carbon
        $dueIn = \Carbon\Carbon::parse($workshop->due_in)->format('d-m-Y H:i:s');
        $dueOut = \Carbon\Carbon::parse($workshop->due_out)->format('d-m-Y H:i:s');

        // Replace underscores with spaces in payment_method and fitting_type
        $paymentMethod = str_replace('_', ' ', $workshop->payment_method);
        $fittingType = str_replace('_', ' ', $workshop->fitting_type);

        // Fetch workshop items
        $items = WorkshopTyre::where('workshop_id', $workshop->id)->get();

        // Fetch workshop services
        $services = WorkshopService::where('workshop_id', $workshop->id)
            ->get();

        // Fetch customer details
        // Fetch customer details if available
        // Fetch customer details if available
        $customer = Customer::where('id', $workshop->customer_id)
            ->select(
                'id',
                'customer_name',
                'customer_email',
                'customer_contact_number',
                'shipping_address_street',
                'shipping_address_city',
                'shipping_address_postcode',
                'shipping_address_county', // County ID
                'shipping_address_country' // Country ID
            )
            ->first();

        // Consolidate workshop address components into a single string
        $fullWorkshopAddress = implode(', ', array_filter([
            $workshop->address,
            $workshop->city,
            $workshop->zone,
            $workshop->county,
            $workshop->country
        ]));

        // If customer exists, consolidate their address fields
        if ($customer) {
            // Fetch county name from regioncounty table
            $countyName = RegionCounty::where('zone_id', $customer->shipping_address_county)->value('name') ?? null;
            // Fetch country name from countries table
            $countryName = Countries::where('country_id', $customer->shipping_address_country)->value('name') ?? null;
            // Consolidate customer address with resolved county and country names
            $fullCustomerAddress = implode(', ', array_filter([
                $customer->shipping_address_street,
                $customer->shipping_address_city,
                $customer->shipping_address_postcode,
                $countyName, // Resolved county name
                $countryName // Resolved country name
            ]));
            // Use the consolidated customer address if available, otherwise fall back to workshop address
            $customer->customer_address = $fullCustomerAddress ?: $fullWorkshopAddress;
        } else {
            // Fallback to workshop address if no customer is found
            $customer = (object) [
                'id' => null,
                'customer_name' => $workshop->name ?? 'N/A',
                'customer_email' => $workshop->email ?? 'N/A',
                'customer_contact_number' => $workshop->mobile ?? 'N/A',
                'customer_address' => $fullWorkshopAddress ?? 'N/A',
            ];
        }

        // Fetch vehicle details
        $vehicle = VehicleDetail::where('vehicle_reg_number', $workshop->vehicle_reg_number)
            ->first();

        // If vehicle is null, use data from workshop table
        $vehicle = $vehicle ?: (object) [
            'id' => null,
            'vehicle_reg_number' => $workshop->vehicle_reg_number ?? '',
            'make' => $workshop->make ?? '',
            'model' => $workshop->model ?? '',
            'year' => $workshop->year ?? '',
        ];

        return response()->json([
            'booking' => [
                'id' => $booking->id,
                'title' => $booking->title,
                'start' => Carbon::parse($booking->start, 'Europe/London')->toIso8601String(),
                'end' => Carbon::parse($booking->end, 'Europe/London')->toIso8601String(),
            ],
            'workshop' => [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'vehicle_reg_number' => $workshop->vehicle_reg_number,
                'due_in' => $dueIn,
                'due_out' => $dueOut,
                'grandTotal' => $workshop->grandTotal,
                'payment_method' => $paymentMethod,
                'status' => $workshop->status,
                'items' => $items,
                'services' => $services,
                'fitting_type' => $fittingType,
            ],
            'customer' => $customer,
            'vehicle' => $vehicle,
        ]);
    }


}