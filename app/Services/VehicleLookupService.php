<?php

namespace App\Services;

use App\Models\VrmApiResponse;
use App\Services\CreditLogService;
use Illuminate\Support\Facades\Log;

class VehicleLookupService
{
    protected $ukVehicleApiService;
    protected $creditLogService;

    public function __construct(UkVehicleApiService $ukVehicleApiService, CreditLogService $creditLogService)
    {
        $this->ukVehicleApiService = $ukVehicleApiService;
        $this->creditLogService = $creditLogService;

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
        $normalizedVrm = strtoupper(preg_replace('/\s+/', '', $vrm));
        $userId = $userId ?? 1;

        try {
            
            $existingRecords = VrmApiResponse::where('vrm', $normalizedVrm)
                ->get();
            $existingDataByPackage = [];
            foreach ($existingRecords as $record) {
                $existingDataByPackage[$record->data_package] = json_decode($record->api_response, true);
            }

            foreach ($packages as $packageName) {
                if (isset($existingDataByPackage[$packageName])) {
                    $mergedData[$packageName] = $existingDataByPackage[$packageName];
                } else {
                    $apiResponse = $this->ukVehicleApiService->fetchVehicleData($normalizedVrm, $packageName);
                    if (!$apiResponse['success']) {
                        return [
                            'success' => false,
                            'error' => $apiResponse['error'],
                        ];
                    }

                    VrmApiResponse::updateOrCreate(
                        ['vrm' => $normalizedVrm, 'data_package' => $packageName],
                        [
                            'api_response' => json_encode($apiResponse['data']),
                            'added_date' => now(),
                        ]
                    );

                    $mergedData[$packageName] = $apiResponse['data'];
                }
            }

            $finalResponse = $this->mergePackageData($mergedData);

            $currentBalance = $this->creditLogService->getAvailableCredit('vrm');

            if ($currentBalance <= 0) {
                return [
                    'success' => false,
                    'error' => 'Your VRM credits have expired. Please purchase more credits.',
                    'alert_type' => 'critical',
                    'balance' => $currentBalance,
                ];
            }

            $lowCreditWarning = $currentBalance <= 10;

            // Deduct credit
            $creditResult = $this->creditLogService->useVrmCredit(
                credit_type: 'vrm',
                vrm: $normalizedVrm,
                userId: $userId,
                reason: "Used 1 vrm credit for {$normalizedVrm}",
                origin: 'api'
            );

            if ($lowCreditWarning) {
                Log::warning('Low VRM credits', $creditResult);
                $creditResult['error'] = '⚠️ Your credits are getting low. Please recharge soon.';
                $creditResult['alert_type'] = 'warning';
            }

            $mergedData['credit_status'] = $creditResult;

            return [
                'success' => true,
                'data' => $finalResponse,
            ];
        } catch (\Exception $e) {
            Log::error('Error processing vehicle details: ', ['error' => $e->getMessage()]);
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
            foreach ($packageData as $sectionName => $sectionData) {
                if (!isset($mergedData[$sectionName])) {
                    $mergedData[$sectionName] = $sectionData;
                } else {
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
                $merged[$key] = $this->deepMergeArrays($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}