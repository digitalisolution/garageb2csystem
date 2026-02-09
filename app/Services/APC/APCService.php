<?php

namespace App\Services\APC;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class APCService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.apc.base_url');
        $this->username = config('services.apc.username');
        $this->password = config('services.apc.password');
    }

    protected function client()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->acceptJson()
            ->timeout(30);
    }

    /** Create Shipment */
    public function createShipment(array $payload): array
    {
        $response = $this->client()->post(
            $this->baseUrl . 'orders',
            $payload
        );

        if (!$response->successful()) {
            Log::error('APC Create Shipment Failed', $response->json());
            throw new \Exception('APC shipment failed');
        }

        return $response->json();
    }

    /** Cancel Shipment */
    public function cancelShipment(string $consignmentNumber): bool
    {
        $response = $this->client()->delete(
            $this->baseUrl . 'orders/' . $consignmentNumber
        );

        if (!$response->successful()) {
            Log::error('APC Cancel Failed', $response->json());
            return false;
        }

        return true;
    }

    /** Rebook Shipment */
    public function rebookShipment(array $payload): array
    {
        return $this->createShipment($payload);
    }
}
