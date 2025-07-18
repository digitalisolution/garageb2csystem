<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UkVehicleApiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('UK_VEHICLE_API_KEY', 'af89fe40-3830-4212-a3a6-a34c305ef580');
        $this->baseUrl = 'https://v2.api.ukvehicledata.co.uk/r2/lookup';
    }

    /**
     * Fetch vehicle data from the updated API.
     *
     * @param string $vrm
     * @param string $packageName
     * @return array
     */
    public function fetchVehicleData(string $vrm, string $packageName): array
    {
        $url = $this->baseUrl;

        try {
            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(30)->get($url, [
                        'packageName' => $packageName,
                        'vrm' => $vrm,
                    ]);

            $rawData = json_decode($response->body(), true);

            if (isset($rawData['response']) && is_string($rawData['response'])) {
                $data = json_decode($rawData['response'], true);

            } else {
                $data = $rawData;
            }

            if (!is_array($data)) {
                return [
                    'success' => false,
                    'error' => 'Invalid API response format.',
                    'raw_response' => $response->body(),
                ];
            }
            
             if (
                isset($data['ResponseInformation']['StatusCode']) &&
                $data['ResponseInformation']['StatusCode'] === 0 ||
                 $data['ResponseInformation']['StatusCode'] === 1 &&
                $data['ResponseInformation']['IsSuccessStatusCode'] === true
            ) {
                // Extract the relevant data from the 'Results' key
                $results = $data['Results'] ?? [];

                return [
                    'success' => true,
                    'data' => $results,
                ];
            }

            // Handle API-level errors
            return [
                'success' => false,
                'error' => $data['ResponseInformation']['StatusMessage'] ?? 'Unknown error occurred.',
                'raw_response' => $response->body(),
            ];
        } catch (\Exception $e) {
            // Log any exceptions that occur during the API request
            Log::error('API Request Failed: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}