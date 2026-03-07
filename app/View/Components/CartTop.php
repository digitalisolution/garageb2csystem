<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Session;
use App\Models\TyresProduct;
use App\Models\CarService;

class CartTop extends ViewComponent
{
    public $cartItems;
    public $shippingData;

    public function __construct()
    {
        $this->cartItems = $this->getCartItems();
        $this->shippingData = Session::get('postcode_data', []);
        $this->garageVatClass = Session::get('garageVatClass');
    }

    private function getCartItems()
    {
        $cart = Session::get('cart', []);
        // dd($cart);
        $garageFittingCharge = Session::get('garageFittingCharge');
        $garageVatClass = Session::get('garageVatClass');
        $cartItems = [];

        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::find($item['id']);
                } else {
                    continue;
                }

                if ($item['type'] === 'tyre') {
                    $image = $product->tyre_image ?? 'sample-tyre.png';
                } elseif ($item['type'] === 'service') {
                    $image = $product->inner_image ?? 'no-img-service.jpg';
                } else {
                    $image = null;
                }

                if ($product) {
                    $cartItems[] = [
                        'id' => $product->id,
                        'type' => $item['type'],
                        'image' => $image,
                        'desc' => $product->description ?? '',
                        'model' => $product->model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'price' => $product->tyre_fullyfitted_price ?? $product->cost_price,
                        'quantity' => $item['quantity'],
                        'total' => ($product->tyre_fullyfitted_price ?? $product->cost_price) * $item['quantity'],
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'tax_class_id' => $product->tax_class_id ?? 0,
                        'garageVatClass' => $garageVatClass,
                        'garageFittingCharge' => $garageFittingCharge,
                    ];
                }
            }
        }

        return $cartItems;
    }

    public function render()
    {
        $subTotal = 0;
        $vatTotal = 0;
        $shippingPricePerJob = 0;
        $shippingPricePerTyre = 0;
        $shippingVAT = 0;
        $garageFittingCharges = 0;
        $garageFittingVAT = 0;

        foreach ($this->cartItems as $item) {
            $subTotal += $item['price'] * $item['quantity'];
            if ($item['tax_class_id'] == 9) {
                $vatTotal += $item['price'] * $item['quantity'] * 0.2;
            }
           
            if ($item['fitting_type'] === 'mobile_fitted') {
                $shippingType = $this->shippingData['ship_type'] ?? 'job';
                $shippingPrice = $this->shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
            if ($item['fitting_type'] === 'mailorder') {
                $shippingType = $this->shippingData['ship_type'] ?? 'job';
                $shippingPrice = $this->shippingData['ship_price'] ?? 0;

                if ($shippingType === 'job') {
                    $shippingPricePerJob = max($shippingPricePerJob, $shippingPrice);
                } elseif ($shippingType === 'tyre' && $item['type'] === 'tyre') {
                    $shippingPricePerTyre += $shippingPrice * $item['quantity'];
                }
            }
            if ($item['type'] === 'tyre' && $item['fitting_type'] === 'fully_fitted') {
                $garageFittingCharges += $item['garageFittingCharge'];
            }
        }

        if (($this->shippingData['includes_vat'] ?? 0) == 9) {
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal += $shippingVAT;
        }

        if (($this->garageVatClass) == 9) {
            $garageFittingVAT = $garageFittingCharges * 0.2;
            $vatTotal += $garageFittingVAT;
        }


        $grandTotal = $subTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre + $garageFittingCharges;
        return $this->ViewComponent('cart-top', [
            'cartItems' => $this->cartItems,
            'shippingData' => $this->shippingData,
            'subTotal' => $subTotal,
            'vatTotal' => $vatTotal,
            'grandTotal' => $grandTotal,
            'shippingPricePerJob' => $shippingPricePerJob,
            'shippingPricePerTyre' => $shippingPricePerTyre,
            'shippingVAT' => $shippingVAT,
            'garageFittingCharges' => $garageFittingCharges,
        ]);
    }
}