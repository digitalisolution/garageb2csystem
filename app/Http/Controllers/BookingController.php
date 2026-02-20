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
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
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
            'title' => 'Booked Slot',
            'start' => Carbon::parse($validated['start']),
            'end' => Carbon::parse($validated['end']),
        ]);

        return response()->json(['success' => true, 'message' => 'Booking saved!', 'booking' => $booking]);
    }

    public function createJob(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        return response()->json(['success' => true, 'message' => 'Job created for booking ID ' . $id]);
    }


    public function getBookingDetails($id)
    {
        $booking = Booking::with(['workshop','garage'])
            ->where('id', $id)
            ->first();
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $workshop = $booking->workshop;

        $dueIn = \Carbon\Carbon::parse($workshop->due_in)->format('d-m-Y H:i:s');
        $dueOut = \Carbon\Carbon::parse($workshop->due_out)->format('d-m-Y H:i:s');

        $paymentMethod = str_replace('_', ' ', $workshop->payment_method);
        $fittingType = str_replace('_', ' ', $workshop->fitting_type);

        $items = WorkshopTyre::where('workshop_id', $workshop->id)->get();
        $services = WorkshopService::where('workshop_id', $workshop->id)
            ->get();
        $customer = Customer::where('id', $workshop->customer_id)
            ->select(
                'id',
                'customer_name',
                'customer_email',
                'customer_contact_number',
                'shipping_address_street',
                'shipping_address_city',
                'shipping_address_postcode',
                'shipping_address_county',
                'shipping_address_country'
            )
            ->first();
        $fullWorkshopAddress = implode(', ', array_filter([
            $workshop->address,
            $workshop->city,
            $workshop->zone,
            $workshop->county,
            $workshop->country
        ]));
        if ($customer) {
            $countyName = RegionCounty::where('zone_id', $customer->shipping_address_county)->value('name') ?? null;
            $countryName = Countries::where('country_id', $customer->shipping_address_country)->value('name') ?? null;
            $fullCustomerAddress = implode(', ', array_filter([
                $customer->shipping_address_street,
                $customer->shipping_address_city,
                $customer->shipping_address_postcode,
                $countyName,
                $countryName
            ]));
            $customer->customer_address = $fullCustomerAddress ?: $fullWorkshopAddress;
        } else {
            $customer = (object) [
                'id' => null,
                'customer_name' => $workshop->name ?? 'N/A',
                'customer_email' => $workshop->email ?? 'N/A',
                'customer_contact_number' => $workshop->mobile ?? 'N/A',
                'customer_address' => $fullWorkshopAddress ?? 'N/A',
            ];
        }

        $vehicle = VehicleDetail::where('vehicle_reg_number', $workshop->vehicle_reg_number)
            ->first();
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
                'garage_name' => $booking->garage?->garage_name ?? 'N/A',
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