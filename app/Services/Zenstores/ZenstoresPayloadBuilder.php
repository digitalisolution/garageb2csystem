<?php

namespace App\Services\Zenstores;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZenstoresPayloadBuilder
{
    public function build(Order $order): array
    {
        return [
            'courier' => 'APC',
            'reference' => 'GA-' . $order->id,

            'sender' => [
                'company_name' => $order->garage->name,
                'address_line_1' => $order->garage->address,
                'city' => $order->garage->city,
                'postcode' => $order->garage->postcode,
                'country_code' => 'GB',
                'phone' => $order->garage->phone,
            ],

            'recipient' => [
                'name' => $order->customer_name,
                'address_line_1' => $order->delivery_address,
                'city' => $order->delivery_city,
                'postcode' => $order->delivery_postcode,
                'country_code' => 'GB',
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],

            'parcels' => $order->parcels->map(fn ($p) => [
                'weight' => $p->weight,
                'length' => $p->length,
                'width' => $p->width,
                'height' => $p->height,
            ])->toArray(),
        ];
    }
}
