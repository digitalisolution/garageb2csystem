<?php

namespace App\Services;

use App\Models\VrmApiResponse; // Shared database model
use Illuminate\Support\Facades\Log;

class VehicleLookupService
{
    protected $ukVehicleApiService;

    public function __construct(UkVehicleApiService $ukVehicleApiService)
    {
        $this->ukVehicleApiService = $ukVehicleApiService;
    }

    /**
     * Lookup vehicle details by VRM for multiple packages.
     *
     * @param string $vrm
     * @param array $packages
     * @return array
     */
    public function lookupVehicleDetailsForPackages(string $vrm, array $packages): array
    {

        $mergedData = [];
        $normalizedVrm = strtoupper($vrm);

        try {
            // Fetch existing records for the normalized VRM
            $existingRecords = VrmApiResponse::where('vrm', $normalizedVrm)
                // Only consider recent records
                ->get();
            $existingDataByPackage = [];
            foreach ($existingRecords as $record) {
                $existingDataByPackage[$record->data_package] = json_decode($record->api_response, true);
            }

            foreach ($packages as $packageName) {
                if (isset($existingDataByPackage[$packageName])) {
                    // Use cached data
                    $mergedData[$packageName] = $existingDataByPackage[$packageName];
                    // dd($mergedData[$packageName]);
                } else {
                    // Fetch new data from the API
                    $apiResponse = $this->ukVehicleApiService->fetchVehicleData($normalizedVrm, $packageName);
                    if (!$apiResponse['success']) {
                        return [
                            'success' => false,
                            'error' => $apiResponse['error'],
                        ];
                    }

                    // Save the full API response into the database
                    VrmApiResponse::updateOrCreate(
                        ['vrm' => $normalizedVrm, 'data_package' => $packageName],
                        [
                            'api_response' => json_encode($apiResponse['data']), // Save full API response
                            'added_date' => now(),
                        ]
                    );

                    $mergedData[$packageName] = $apiResponse['data'];
                }
            }

            // Merge all package data into a single response
            $finalResponse = $this->mergePackageData($mergedData);
            // dd($finalResponse);
            return [
                'success' => true,
                'data' => $finalResponse,
            ];
        } catch (\Exception $e) {
            Log::error('Error processing vehicle details: ', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'An unexpected error occurred while processing the request.',
            ];
        }
    }

    /**
     * Merge data from multiple packages into a single structured response.
     *
     * @param array $packageDataArray
     * @return array
     */
    private function mergePackageData(array $packageDataArray): array
    {
        $mergedData = [];

        foreach ($packageDataArray as $packageName => $packageData) {
            // Iterate through each section in the package data
            foreach ($packageData as $sectionName => $sectionData) {
                if (!isset($mergedData[$sectionName])) {
                    // If the section doesn't exist in the merged data, initialize it
                    $mergedData[$sectionName] = $sectionData;
                } else {
                    // Merge the section data with the existing data
                    $mergedData[$sectionName] = $this->deepMergeArrays($mergedData[$sectionName], $sectionData);
                }
            }
        }

        return $mergedData;
    }

    /**
     * Deeply merge two arrays recursively.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function deepMergeArrays(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                // Recursively merge arrays
                $merged[$key] = $this->deepMergeArrays($merged[$key], $value);
            } else {
                // Overwrite or add the value
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}