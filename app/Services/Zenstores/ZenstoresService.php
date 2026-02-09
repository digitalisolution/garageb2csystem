<?php

namespace App\Services\Zenstores;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZenstoresService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.zenstores.base_url');
        $this->apiKey  = config('services.zenstores.api_key');
    }

    protected function client()
    {
        return Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout(30);
    }

    public function createShipment(array $payload): array
    {
        $response = $this->client()->post(
            $this->baseUrl . 'shipments',
            $payload
        );

        if (!$response->successful()) {
            Log::error('Zenstores Create Shipment Failed', $response->json());
            throw new \Exception('Zenstores shipment failed');
        }

        return $response->json();
    }

    public function cancelShipment(string $shipmentId): bool
    {
        $response = $this->client()->delete(
            $this->baseUrl . 'shipments/' . $shipmentId
        );

        if (!$response->successful()) {
            Log::error('Zenstores Cancel Shipment Failed', $response->json());
            return false;
        }

        return true;
    }
}
