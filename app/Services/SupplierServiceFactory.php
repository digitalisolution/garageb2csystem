<?php
namespace App\Services;

use App\Services\BondService;
use App\Services\EdenService;
use App\Services\BitsService;
use Illuminate\Support\Facades\log;
use Illuminate\Support\Facades\DB;

class SupplierServiceFactory
{
    protected function getDynamicCredentials($supplierName)
    {
        // Fetch the JSON data from the database
        $apiOrderDetails = DB::table('suppliers')
            ->where('supplier_name', $supplierName)
            ->value('api_order_details');

        if ($apiOrderDetails) {
            try {
                // Decode the JSON data into an array
                $credentials = json_decode($apiOrderDetails, true);

                if ($credentials === null) {
                    // Log an error if JSON decoding fails
                    Log::error('JSON decode failed for supplier credentials.', ['supplier_name' => $supplierName, 'data' => $apiOrderDetails]);
                    return null; // Return null if JSON decoding fails
                }

                // Validate required fields in the decoded data
                if (isset($credentials['bond_api_mode'], $credentials['bond_api_code'])) {
                    return [
                        'supplier_email' => $credentials['bond_supplieremail'] ?? null,
                        'api_mode' => $credentials['bond_api_mode'],
                        'api_code' => $credentials['bond_api_code'],
                        'trading_point' => $credentials['trading_point'] ?? null,
                        'auto_order' => $credentials['bond_status_autoorder'] == "1",
                    ];
                }elseif (isset($credentials['eden_status_autoorder'], $credentials['eden_dir_path'])) {
                    return [
                        'eden_upload_mode' => $credentials['eden_upload_mode'] ?? null,
                        'external_ref_append' => $credentials['external_ref_append']?? null,
                        'eden_dir_path' => $credentials['eden_dir_path']?? null,
                        'item_type' => $credentials['item_type'] ?? null,
                        'auto_order' => $credentials['eden_status_autoorder'] == "1",
                    ];
                } else {
                    // Log error if required fields are missing
                    Log::error("Missing required fields in decoded credentials", ['supplier_name' => $supplierName, 'credentials' => $credentials]);
                }
            } catch (\Exception $e) {
                // Log any errors that occur during JSON decoding
                Log::error('JSON decode error', ['error' => $e->getMessage(), 'data' => $apiOrderDetails]);
            }
        }

        return null;
    }




    public function getServiceForSupplier($supplierName)
    {
        $credentials = $this->getDynamicCredentials($supplierName);

        if ($credentials) {
            switch (strtolower($supplierName)) {
                case 'bond':
                    return new BondService($credentials);
                case 'eden':
                    return new EdenService($credentials);
                case 'bits':
                    return new BitsService($credentials);
                // Add cases for other suppliers as needed
            }
        }

        return null;
    }
}
