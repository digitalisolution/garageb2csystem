<?php

namespace App\Services\APC;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class APCPayloadBuilder
{
    public function build(Order $order): array
    {
        return [
            'order' => [
                'collection_date' => now()->addDay()->format('Y-m-d'),
                'account' => config('services.apc.account'),
                'service' => $order->apc_service_code,
                'reference' => 'GA-' . $order->id,

                'collection' => [
                    'company_name' => config('app.name'),
                    'address_1' => $order->garage->address,
                    'city' => $order->garage->city,
                    'postcode' => $order->garage->postcode,
                    'country' => 'GB',
                    'telephone' => $order->garage->phone,
                ],

                'delivery' => [
                    'company_name' => $order->customer_name,
                    'address_1' => $order->delivery_address,
                    'city' => $order->delivery_city,
                    'postcode' => $order->delivery_postcode,
                    'country' => 'GB',
                    'telephone' => $order->customer_phone,
                    'email' => $order->customer_email,
                ],

                'parcels' => $order->parcels->map(fn($p) => [
                    'weight' => $p->weight,
                    'length' => $p->length,
                    'width' => $p->width,
                    'height' => $p->height,
                ])->toArray(),
            ]
        ];
    }
}
