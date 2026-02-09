<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DistanceService
{
    /**
     * Calculate distance in miles between two postcodes.
     *
     * @param string $origin
     * @param string $destination
     * @return float|null
     */
    public function getDistanceMiles(string $origin, string $destination): ?float
    {
        if (empty($origin) || empty($destination)) {
            return null;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination . ', UK',
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (
                isset($data['rows'][0]['elements'][0]['status']) &&
                $data['rows'][0]['elements'][0]['status'] === 'OK'
            ) {
                $meters = $data['rows'][0]['elements'][0]['distance']['value'];
                return round($meters / 1609.34, 2); // meters → miles
            }
        }

        return null;
    }
}
