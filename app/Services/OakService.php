<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OakService
{
    protected array $config;
    protected string $baseUrl;
    protected string $siteId = '';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = 'https://api.thevirtualwarehouse.co.uk/';
    }

    public function getLoginToken(): string
    {
        $response = Http::post($this->baseUrl . '/api/GetLoginToken', [
            'username' => $this->config['oak_api_username'],
            'password' => $this->config['oak_api_password'],
            'apiKey'   => $this->config['oak_api_key'],

        ]);

        if (! $response->successful()) {
            Log::error('OAK Login Failed', [
                'response' => $response->body(),
            ]);
            throw new \Exception('OAK login failed');
        }

        return trim($response->body(), '"');
    }

   public function getSiteIdDetails(): array
    {
        $token = $this->getLoginToken();

        $response = Http::withToken($token)
            ->get($this->baseUrl . '/api/GetCusSiteDetails');
        //dd($response);
        if (! $response->successful()) {
            Log::error('OAK Get Sites Failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'error' => true,
                'msg' => 'Failed to fetch site details'
            ];
        }

        return $response->json();
    }

    public function placeApiOrder(string $reference, array $products, string $jobId = null): array
    {
        Log::info("Oak Processing Order", ['reference' => $reference, 'url' => $this->baseUrl]);

        if (empty($products)) {
            return [
                'api_order_id' => '',
                'msg'          => 'No products provided.',
                'status'       => 'danger',
                'type'         => 'api',
                'details'      => []
            ];
        }

        $siteData = $this->getSiteIdDetails();
        $this->siteId = $this->config['oak_siteid'] ?? ($siteData[0]['siteId'] ?? '');

        if (!$this->siteId) {
            return [
                'api_order_id' => '',
                'msg'          => 'No valid SiteID found',
                'status'       => 'danger',
                'type'         => 'api',
                'details'      => []
            ];
        }

        $this->siteId = $siteData[0]['siteId'] ?? '';

        $lines = collect($products)->map(function ($product, $key) {
            return [
                'Code'              => $product['sku'],
                'Qty'               => (int) $product['quantity'],
                'Price'             => $product['price'],
                'Cost'              => $product['price'],
                'NoStockControl'    => true,
                'TyreCatalogueRef'  => '',
                'CusLineID'         => '',
                'EanBarcode'        => $product['ean'] ?? '',
                'SupplierRef'       => '',
                'ReservationNumber' => '',
                'ReservationExpiry' => ''
            ];
        })->toArray();

        $responseDetails = collect($products)->map(function ($product) {
            return [
                'sku'      => $product['sku'],
                'quantity' => $product['quantity'],
                'ean'      => $product['ean'] ?? '',
                'price'    => $product['price'] ?? '',
                'supplier' => 'bmtr'
            ];
        })->values()->toArray();

        $orderPayload = [
            'SiteID'                     => $this->siteId,
            'Comments'                   => $reference,
            'Lines'                      => $lines,
            'SendOrderConfirmationToSite'=> true
        ];

        Log::info("Oak Order Request", ['payload' => $orderPayload]);

        $token = $this->getLoginToken();
        $url   = "{$this->baseUrl}/api/PlaceOrder";

        $response = Http::withoutVerifying()
            ->withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $orderPayload);

        Log::info("Oak Order Response", ['response' => $response]);

        if ($response->successful() && is_numeric($response->body())) {
            $apiOrderId = $response->body();

            foreach ($responseDetails as &$detail) {
                $detail['api_order_id'] = $apiOrderId;
                $detail['reference']    = 'JOB-' . $reference;
            }

            return [
                'api_order_id' => $apiOrderId,
                'msg'          => 'Order Placed.',
                'status'       => 'success',
                'type'         => 'api',
                'details'      => $responseDetails
            ];
        }

        return [
            'api_order_id' => '',
            'msg'          => 'Order Not Placed, Some error',
            'status'       => 'danger',
            'type'         => 'api',
            'details'      => $responseDetails
        ];
    }
}

