<?php

namespace App\Services;

use App\Models\VrmVehicleDetail;
use App\Models\TyresProduct;
use App\Models\GarageDetails;

class SharedDataService
{
    public function getSharedData($domain)
    {
        // Fetch garage details based on domain
        $garage = GarageDetails::where('domain', $domain)->first();

        // Fetch cart details
        $cart = session('cart', []);
        $totalQuantity = array_sum(array_column($cart, 'quantity'));
        $totalPrice = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

        // Fetch vehicle details (consider optimizing for large datasets)
        $vehicleDetails = VrmVehicleDetail::all();

        return [
            'cart' => $cart,
            'cartTotalQuantity' => $totalQuantity,
            'cartTotalPrice' => number_format($totalPrice, 2),
            'garage' => $garage,
            'vehicleDetails' => $vehicleDetails,
        ];
    }
}
