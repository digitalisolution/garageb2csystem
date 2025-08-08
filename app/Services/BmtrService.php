<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BmtrService
{
    private $username;
    private $password;
    private $apiKey;
    private $apiUrl;
    private $siteId;
    private $tokenFile = 'bmtr_token.txt'; // stored in storage/app

    public function __construct(array $supplierDetails)
    {
        //dd($supplierDetails);
        $this->username      = $supplierDetails['bmtr_api_username'] ?? null;
        $this->password      = $supplierDetails['bmtr_api_password'] ?? null;
        $this->apiKey        = $supplierDetails['bmtr_api_key'] ?? null;
        $this->siteId        = $supplierDetails['bmtr_siteid'] ?? null;
        $apiMode = $supplierDetails['bmtr_api_mode'] ?? 'test';
        $this->apiUrl = $apiMode === 'live'
            ? 'https://api.thevirtualwarehouse.co.uk'
            : 'http://qaapi.thevirtualwarehouse.co.uk';
            //dd($apiMode);
    }

    /**
     * Retrieve saved token
     */
    private function getToken(): ?string
    {
        return Storage::exists($this->tokenFile)
            ? Storage::get($this->tokenFile)
            : null;
    }

    /**
     * Save new login token
     */
    private function saveLoginToken(): bool
{
    $url = "{$this->apiUrl}/api/GetLoginToken";

    $data = [
        'username'   => $this->username,
        'password'   => $this->password,
        'apiKey'     => $this->apiKey,
        'SiteID'     => '181620',//$this->siteId,
        'forceLogin' => true,
    ];

    Log::info("BMTR Save Login Token Request", $data);

    $response = Http::withoutVerifying()
        ->acceptJson()
        ->post($url, $data);

    if ($response->failed()) {
        Log::error("BMTR Token Request Failed", [
            'status' => $response->status(),
            'body'   => $response->body()
        ]);
        return false;
    }

    $json = $response->json();
    if (isset($json['Error'])) {
        Log::error("BMTR Token Error", ['error' => $json['Error']]);
        return false;
    }

    $token = $json['Token'] ?? null;
    if (!$token) {
        Log::error("BMTR No Token Found", ['body' => $response->body()]);
        return false;
    }

    Storage::put($this->tokenFile, $token);
    Log::info("BMTR token saved successfully.");
    return true;
}


    /**
     * Get Site Details
     */
    private function getSiteDetails()
{
    if (!$this->saveLoginToken()) {
        return null;
    }

    $token = $this->getToken();
    if (!$token) {
        Log::error("BMTR No valid token available.");
        return null;
    }

    $url = "{$this->apiUrl}/api/GetCusSiteDetails";
    $response = Http::withoutVerifying()
        ->withToken($token)
        ->get($url);

    if ($response->failed()) {
        Log::error("BMTR Site Details Failed", [
            'status' => $response->status(),
            'body'   => $response->body()
        ]);
        return null;
    }

    return $response->json();
}


    /**
     * Place API Order
     */
    public function placeApiOrder(string $reference, array $products, string $jobId = null): array
    {
        Log::info("BMTR Processing Order", ['reference' => $reference, 'url' => $this->apiUrl]);

        if (empty($products)) {
            return ['api_order_id' => '', 'msg' => 'No products provided.', 'status' => 'danger', 'type' => 'api'];
        }

        /*$siteData = $this->getSiteDetails();
        //dd($siteData);
        $this->siteId = $siteData[0]['siteId'] ?? '181620';*/
        $siteData = $this->getSiteDetails();
        if (empty($siteData[0]['siteId'])) {
            return ['api_order_id' => '', 'msg' => 'No valid SiteID found', 'status' => 'danger', 'type' => 'api'];
        }

        $this->siteId = $siteData[0]['siteId'] ?? '181620';;


        $lines = collect($products)->map(function ($product) {
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

        $orderPayload = [
            'SiteID'                     => '181620',//$this->siteId,
            'Comments'                   => $reference,
            'Lines'                      => $lines,
            'SendOrderConfirmationToSite'=> true
        ];

        $token = $this->getToken();
        $url   = "{$this->apiUrl}/api/PlaceOrder";

        $response = Http::withoutVerifying()
            ->withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $orderPayload);

        Log::info("BMTR Order Response", ['response' => $response->body()]);

        if ($response->successful() && is_numeric($response->body())) {
            return [
                'api_order_id' => $response->body(),
                'msg'          => 'Order Placed.',
                'status'       => 'success',
                'type'         => 'api'
            ];
        }

        return [
            'api_order_id' => '',
            'msg'          => 'Order Not Placed, Some error',
            'status'       => 'danger',
            'type'         => 'api'
        ];
    }
}
