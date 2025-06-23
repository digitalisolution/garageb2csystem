<?php

namespace App\Http\Controllers;

use App\Models\VehicleDetail; // Import the VehicleDetail model
use App\Services\VehicleLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class VrmController extends Controller
{
    protected $vehicleLookupService;

    public function __construct(VehicleLookupService $vehicleLookupService)
    {
        $this->vehicleLookupService = $vehicleLookupService;
    }

    /**
     * Get vehicle details by VRM for multiple packages.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVehicleDetails(Request $request)
    {
        $vrm = $request->input('vrm');
        $packages = ['TyreData'];

        if (empty($vrm) || empty($packages)) {
            return response()->json(['success' => false, 'error' => 'VRM and packages are required.'], 400);
        }

        $result = $this->vehicleLookupService->lookupVehicleDetailsForPackages($vrm, $packages);

        if ($result['success']) {
            // Save the fetched data into the VehicleDetail table
            $this->saveVehicleDetails($vrm, $result['data']);

            return response()->json(['success' => true, 'data' => $result['data']]);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch data.'], 400);
    }

    /**
     * Get vehicle and MOT details by VRM for multiple packages.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVehicleAndMotDetails(Request $request)
    {
        $vrm = $request->input('vrm');
        $packages = ['TyreData', 'VehicleAndMotHistory'];

        if (empty($vrm) || empty($packages)) {
            return response()->json(['success' => false, 'error' => 'VRM and packages are required.'], 400);
        }

        $result = $this->vehicleLookupService->lookupVehicleDetailsForPackages($vrm, $packages);

        if ($result['success']) {
            // Save the fetched data into the VehicleDetail table
            $this->saveVehicleDetails($vrm, $result['data']);

            return response()->json(['success' => true, 'data' => $result['data']]);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch data.'], 400);
    }

    /**
     * Save vehicle details into the VehicleDetail table.
     *
     * @param string $vrm
     * @param array $data
     * @return void
     */
    private function saveVehicleDetails(string $vrm, array $data): void
    {
        // Map the API response fields to the VehicleDetail table columns
        $vehicleData = [
            'vehicle_reg_number' => strtoupper($vrm),
            'vehicle_category' => $data['RapidVehicleDetails']['VehicleClass']?? null,
            'vehicle_make' => $data['VehicleDetails']['VehicleIdentification']['DvlaMake'] ?? null,
            'vehicle_model' => $data['VehicleDetails']['VehicleIdentification']['DvlaModel'] ?? null,
            'vehicle_year' => $data['VehicleDetails']['VehicleIdentification']['YearOfManufacture'] ?? null,
            'vehicle_first_registered' => Carbon::parse($data['VehicleDetails']['VehicleIdentification']['DateFirstRegisteredInUk'])->format('Y-m-d H:i:s') ?? null,
            'vehicle_cc' => $data['SmmtDetails']['TechnicalDetails']['EngineCapacityCc'] ?? null,
            'vehicle_fuel_type' => $data['SmmtDetails']['TechnicalDetails']['FuelType'] ?? null,
            'vehicle_body_type' => $data['SmmtDetails']['TechnicalDetails']['BodyStyle'] ?? null,
            'vehicle_bhp' => $data['SmmtDetails']['Performance']['PowerBhp'] ?? null,
            'vehicle_engine_number' => $data['VehicleDetails']['VehicleIdentification']['EngineNumber'] ?? null,
            'vehicle_engine_size' => $data['VehicleDetails']['DvlaTechnicalDetails']['EngineCapacityCc'] ?? null,
            'vehicle_axle' => $data['ModelDetails']['Powertrain']['Transmission']['DriveType'] ?? null,
            'vehicle_vin' => $data['VehicleDetails']['VehicleIdentification']['VinLast5'] ?? null,
            'vehicle_front_tyre_size' => $data['TyreDetails']['TyreDetailsList'][0]['Front']['Tyre']['SizeDescription'] ?? null,
            'vehicle_rear_tyre_size' => $data['TyreDetails']['TyreDetailsList'][0]['Rear']['Tyre']['SizeDescription'] ?? null,
            'vehicle_colour' => $data['RapidVehicleDetails']['Colour']?? null,
            'vehicle_torque_settings' => $data['SmmtDetails']['Performance']['TorqueNm'] ?? null,
            'vehicle_chassis_no' => $data['VehicleDetails']['VehicleIdentification']['Vin'] ?? null,
            'vehicle_mot_expiry_date' => !empty($data['MotHistoryDetails']['MotDueDate'])
                ? Carbon::parse($data['MotHistoryDetails']['MotDueDate'])->format('Y-m-d H:i:s')
                : null,
                    ];

        VehicleDetail::firstOrCreate(
            ['vehicle_reg_number' => strtoupper($vrm)],
            $vehicleData 
        );
    }
}