<?php

namespace App\Services;

class CartTotalService
{
    public function recalculate()
    {
        $cart = session('cart', []);

        $cartSubTotal        = session('cartSubTotal', 0);
        $vatTotal            = session('vatTotal', 0);
        $shippingPricePerJob = session('shippingPricePerJob', 0);
        $shippingPricePerTyre= session('shippingPricePerTyre', 0);

        $garageChargePerTyre = session('garage_fitting_charge', 0);
        $garageVatClass      = session('garage_fitting_vat_class', 0);

        $totalFullyFittedTyres = 0;

        foreach ($cart as $item) {
            if (
                $item['type'] === 'tyre' &&
                ($item['fitting_type'] ?? '') === 'fully_fitted'
            ) {
                $totalFullyFittedTyres += $item['quantity'];
            }
        }

        $garageFittingCharge = $garageChargePerTyre * $totalFullyFittedTyres;

        $garageFittingVAT = 0;

        if ($garageFittingCharge > 0 && $garageVatClass == 9) {
            $garageFittingVAT = $garageFittingCharge * 0.20;
            $vatTotal += $garageFittingVAT;
        }

        $grandTotal =
            $cartSubTotal +
            $vatTotal +
            $shippingPricePerJob +
            $shippingPricePerTyre +
            $garageFittingCharge;

        session([
            'garageFittingCharge' => $garageFittingCharge,
            'garageFittingVAT'    => $garageFittingVAT,
            'garageVatClass'      => $garageVatClass,
            'vatTotal'            => $vatTotal,
            'cartTotalPrice'      => $grandTotal,
        ]);

       // At the end of recalculate(), modify the return array:
return [
    'totalFullyFittedTyres' => $totalFullyFittedTyres,
    'garageFittingCharge'   => number_format($garageFittingCharge, 2),
    'garageFittingVAT'      => number_format($garageFittingVAT, 2),
    'vatTotal'              => number_format($vatTotal, 2),
    'cartTotalPrice'        => number_format($grandTotal, 2),
    'cartSubTotal'          => number_format($cartSubTotal, 2), // Add this line
];
    }
}
