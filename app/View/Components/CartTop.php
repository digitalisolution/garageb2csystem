<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Session;
use App\Models\TyresProduct;
use App\Models\CarService;

class CartTop extends ViewComponent
{
    public $cartItems;
    public $shippingData; // Add a property for shipping data

    public function __construct()
    {
        // Log cart items for debugging purposes
        // \Log::info('Cart Items: ', Session::get('cart', []));

        // Retrieve cart items and shipping data
        $this->cartItems = $this->getCartItems();
        $this->shippingData = Session::get('postcode_data', []); // Get shipping data from session
    }

    private function getCartItems()
    {
        $cart = Session::get('cart', []);
        // dd($cart);
        $cartItems = [];

        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::find($item['id']);
                } else {
                    continue; // Skip unknown types
                }

                if ($product) {
                    $cartItems[] = [
                        'id' => $product->id,
                        'type' => $item['type'],
                        'desc' => $product->description ?? '',
                        'model' => $product->model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'price' => $product->tyre_fullyfitted_price ?? $product->cost_price,
                        'quantity' => $item['quantity'],
                        'total' => ($product->tyre_fullyfitted_price ?? $product->cost_price) * $item['quantity'],
                        'fitting_type' => $item['fitting_type'] ?? null, // Include fitting type
                        'tax_class_id' => $product->tax_class_id ?? 0, // Include tax class ID
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
        }

        if (($this->shippingData['includes_vat'] ?? 0) == 9) {
            $shippingVAT = ($shippingPricePerJob + $shippingPricePerTyre) * 0.2;
            $vatTotal = $vatTotal + $shippingVAT;
        }


        $grandTotal = $subTotal + $vatTotal + $shippingPricePerJob + $shippingPricePerTyre;

        return $this->ViewComponent('cart-top', [
            'cartItems' => $this->cartItems,
            'shippingData' => $this->shippingData,
            'subTotal' => $subTotal,
            'vatTotal' => $vatTotal,
            'grandTotal' => $grandTotal,
            'shippingPricePerJob' => $shippingPricePerJob,
            'shippingPricePerTyre' => $shippingPricePerTyre,
            'shippingVAT' => $shippingVAT,
        ]);
    }
}