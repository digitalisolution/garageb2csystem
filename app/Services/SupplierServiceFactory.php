<?php
namespace App\Services;

use App\Services\BondService;
use App\Services\EdenService;
use App\Services\TyresoftService;
use App\Services\BmtrService;
use App\Services\BitsService;
use App\Services\OakService;
use Illuminate\Support\Facades\log;
use Illuminate\Support\Facades\DB;

class SupplierServiceFactory
{
    protected function getDynamicCredentials($supplierName)
    {
        $apiOrderDetails = DB::table('suppliers')->where('supplier_name', $supplierName)->value('api_order_details');

        if ($apiOrderDetails) {
            try {
                $credentials = json_decode($apiOrderDetails, true);
                if ($credentials === null) {
                    Log::error('JSON decode failed for supplier credentials.', ['supplier_name' => $supplierName, 'data' => $apiOrderDetails]);
                    return null; 
                }

                if (isset($credentials['bond_api_mode'], $credentials['bond_api_code'])) {
                    return [
                        'supplier_email' => $credentials['bond_supplieremail'] ?? null,
                        'api_mode' => $credentials['bond_api_mode'],
                        'api_code' => $credentials['bond_api_code'],
                        'trading_point' => $credentials['trading_point'] ?? null,
                        'auto_order' => $credentials['bond_status_autoorder'] == "1",
                    ];
                }elseif (isset($credentials['eden_status_autoorder'], $credentials['eden_upload_mode'])) {
                    return [
                    'eden_upload_mode' => $credentials['eden_upload_mode'],
                    'external_ref_append' => $credentials['external_ref_append'] ?? null,
                    'eden_dir_path' => $credentials['eden_dir_path'] ?? null,
                    'item_type' => $credentials['item_type'] ?? null,
                    'eden_ftp_host' => $credentials['eden_ftp_host'] ?? null,
                    'eden_ftp_username' => $credentials['eden_ftp_username'] ?? null,
                    'eden_ftp_password' => $credentials['eden_ftp_password'] ?? null,
                    'auto_order' => $credentials['eden_status_autoorder'] == "1",
                ];
                }elseif (isset($credentials['tyresoft_status_autoorder'], $credentials['tyresoft_upload_mode'])) {
                    return [
                    'tyresoft_upload_mode' => $credentials['tyresoft_upload_mode'],
                    'external_ref_append' => $credentials['external_ref_append'] ?? null,
                    'tyresoft_dir_path' => $credentials['tyresoft_dir_path'] ?? null,
                    'item_type' => $credentials['item_type'] ?? null,
                    'tyresoft_ftp_host' => $credentials['tyresoft_ftp_host'] ?? null,
                    'tyresoft_ftp_username' => $credentials['tyresoft_ftp_username'] ?? null,
                    'tyresoft_ftp_password' => $credentials['tyresoft_ftp_password'] ?? null,
                    'auto_order' => $credentials['tyresoft_status_autoorder'] == "1",
                ];
                }elseif (isset($credentials['bmtr_status_autoorder'], $credentials['bmtr_api_mode'])) {
                    return [
                        'bmtr_api_mode' => $credentials['bmtr_api_mode'],
                        'bmtr_siteid' => $credentials['bmtr_siteid'],
                        'bmtr_api_username' => $credentials['bmtr_api_username'],
                        'bmtr_api_password' => $credentials['bmtr_api_password'],
                        'bmtr_api_key' => $credentials['bmtr_api_key'],
                        'auto_order' => $credentials['bmtr_status_autoorder'] == "1",
                    ];
                }elseif (isset($credentials['oak_status_autoorder'], $credentials['oak_api_mode'])) {
                    return [
                        'oak_api_mode' => $credentials['oak_api_mode'],
                        'oak_siteid' => $credentials['oak_siteid'],
                        'oak_api_username' => $credentials['oak_api_username'],
                        'oak_api_password' => $credentials['oak_api_password'],
                        'oak_api_key' => $credentials['oak_api_key'],
                        'auto_order' => $credentials['oak_status_autoorder'] == "1",
                    ];
                } else {
                    Log::error("Missing required fields in decoded credentials", ['supplier_name' => $supplierName, 'credentials' => $credentials]);
                }
            } catch (\Exception $e) {
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
                case 'bmtr':
                    return new BmtrService($credentials);
                case 'bits':
                    return new BitsService($credentials);
                case 'oak':
                    return new OakService($credentials);
                case 'tyresoft':
                    return new TyresoftService($credentials);
            }
        }
        return null;
    }
}
