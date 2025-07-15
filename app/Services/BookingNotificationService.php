<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopTyre;
use App\Models\WorkshopService;
use Carbon\Carbon;

class BookingNotificationService
{

public function getLatestBookings()
{
    return Workshop::where('is_read', false)
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($booking) {

            // Fetch all related tyres
            $tyres = WorkshopTyre::where('workshop_id', $booking->id)->get();

            $tyreQuantity = $tyres->sum('quantity');
            $tyreAmount = $tyres->sum(function ($tyre) {
                $price = $tyre->price;
                if ($tyre->tax_class_id == 9) {
                    $price += $price * 0.20;
                }
                return $price * $tyre->quantity;
            });
            $tyreTypes = $tyres->pluck('product_type')->unique()->implode(', ');
            $tyreDescriptions = $tyres->pluck('description')->filter()->unique()->implode('; ');

            // Fetch all related services
            $services = WorkshopService::where('workshop_id', $booking->id)->get();

            $serviceQuantity = $services->sum('service_quantity');
            $serviceAmount = $services->sum(function ($service) {
                $price = $service->service_price;
                if ($service->tax_class_id == 9) {
                    $price += $price * 0.20;
                }
                return $price * $service->service_quantity;
            });
            $serviceTypes = $services->pluck('product_type')->unique()->implode(', ');
            $serviceDescriptions = $services->pluck('service_name')->filter()->unique()->implode('; ');

            // Combine totals
            $totalQuantity = $tyreQuantity + $serviceQuantity;
            $totalAmount = $tyreAmount + $serviceAmount;

            $paymentStatus = match ($booking->payment_status) {
                1 => 'Paid',
                3 => 'Partially Paid',
                default => 'Unpaid',
            };

            // Combine details
            $combinedTypes = trim(implode(', ', array_filter([$tyreTypes, $serviceTypes])), ', ');
            $combinedDescriptions = trim(implode('; ', array_filter([$tyreDescriptions, $serviceDescriptions])), '; ');

            return (object) [
                'id' => $booking->id,
                'name' => $booking->name,
                'vrm' => $booking->vehicle_reg_number,
                'type' => $combinedTypes ?: 'N/A',
                'date' => $booking->workshop_date
                    ? Carbon::parse($booking->workshop_date)->format('d-m-Y H:i')
                    : null,
                'quantity' => $totalQuantity,
                'description' => $combinedDescriptions ?: 'No description',
                'grandTotal' => number_format($totalAmount, 2),
                'paymentStatus' => $paymentStatus,
            ];
        });
}



}
