<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TyrePricingController;
use Illuminate\Support\Str;
use App\Models\Supplier;
use App\Models\TyresProduct;
use App\Models\tyre_brands;
use App\Services\FTPFetcher;

class TyreImportController extends Controller
{
    private $ftpConnection;

    public function __construct(FTPFetcher $ftpConnection)
    {
        $this->ftpConnection = $ftpConnection;
    }

    /**
     * Install the supplier data by importing tyres.
     */
    public function install($id)
    {
        // Fetch the supplier by ID or throw an exception if not found
        $supplier = Supplier::findOrFail($id);

        try {
           
            $importMethod = $supplier->import_method;

            if ($importMethod === 'csv') {
            } elseif ($importMethod === 'ftp') {
                $ftpDetails = [
                    'ftp_host' => $supplier->ftp_host,
                    'ftp_user' => $supplier->ftp_user,
                    'ftp_password' => $supplier->ftp_password,
                    'ftp_directory' => $supplier->ftp_directory,
                ];
                $this->importFromFtp($ftpDetails, $supplier->id, $supplier->supplier_name);
            } elseif ($importMethod === 'file_path') {
                // Import from a direct file path
                $filePath = $supplier->file_path;
                if (!$filePath) {
                    throw new \Exception('File path is missing.');
                }
                $this->importFromFilePath($filePath, $supplier->id, $supplier->supplier_name);
            } else {
                throw new \Exception('Unknown import method specified.');
            }

            // Check if products exist for the supplier in the `tyres_product` table
            $productCount = TyresProduct::where('tyre_supplier_name', $supplier->supplier_name)
                ->where('supplier_id', $supplier->id)
                ->count();

            if ($productCount > 0) {
                // Update supplier status to active and products to active
                $supplier->status = 1;
                $supplier->save();

                TyresProduct::where('tyre_supplier_name', $supplier->supplier_name)
                    ->where('supplier_id', $supplier->id)
                    ->update(['status' => 1]);

                // Log::info('Install completed successfully', ['supplier_name' => $supplier->supplier_name]);

                try {
                    $tyrePricingController = new TyrePricingController();
                    $tyrePricingController->processTyrePricingUpdate();
                    // Log::info('Prices updated successfully after tyre import.');
                } catch (\Exception $e) {
                    Log::error('Failed to update prices after import', ['error' => $e->getMessage()]);
                }
                return redirect()->back()->with('message.level', 'success')->with('message.content', 'Supplier and Tyre Products installed successfully!');
            } else {
                throw new \Exception('No products inserted for the supplier.');
            }
        } catch (\Exception $e) {
            // Log error and set statuses to inactive (0)
            Log::error('Install failed', ['error' => $e->getMessage(), 'supplier_name' => $supplier->supplier_name]);

            $supplier->status = 0;
            $supplier->save();

            TyresProduct::where('tyre_supplier_name', $supplier->supplier_name)
                ->where('supplier_id', $supplier->id)
                ->update(['status' => 0]);

            return redirect()->back()->withErrors(['error' => 'Install failed: ' . $e->getMessage()]);
        }
    }


    public function uninstall($id)
    {
        // Find the supplier
        $supplier = Supplier::findOrFail($id);

        // Update the supplier's status to inactive (0)
        $supplier->status = 0;
        $supplier->save();

        // Delete tyres related to the supplier in tyres_product table
        TyresProduct::where('tyre_supplier_name', $supplier->supplier_name)
            ->where('supplier_id', $supplier->id)
            ->delete();

        return redirect()->back()->with('message.level', 'success')->with('message.content', 'Supplier and Tyre Products uninstalled successfully!');
    }

    /**
     * Import tyres using CSV file from a specified path.
     */
    private function importFromCsv($filePath, $supplierId, $supplierName)
    {
        if (!file_exists($filePath)) {
            throw new \Exception('CSV file does not exist');
        }

        $fileContent = file_get_contents($filePath);
        $csvData = $this->parseCsv($fileContent);
        switch (strtolower($supplierName)) {
            case 'bond':
                $this->importBondCsv($csvData, $supplierId, $supplierName);
                break;
                case 'bits':
                $this->importBitsCsv($csvData, $supplierId, $supplierName);
                break;
                case 'etb':
                    $this->importEtbCsv($csvData, $supplierId, $supplierName);
                break;
                case 'eden':
                    $this->importEdenCsv($csvData, $supplierId, $supplierName);
                break;
                case 'easityre':
                    $this->importEasityreCsv($csvData, $supplierId, $supplierName);
                break;
                case 'bmtr':
                    $this->importBmtrCsv($csvData, $supplierId, $supplierName);
                break;
                // case 'ownstock':
                // $this->importOwnStockCsv($csvData, $supplierId, $supplierName);
                // break;
            default:
                Log::warning('Unknown supplier name', ['supplier_name' => $supplierName]);
                throw new \Exception('Unknown supplier name: ' . $supplierName);
        }

        // Step 6: Log success
        // Log::info('CSV data imported successfully', ['supplier_name' => $supplierName]);
        // Import the data
        // $this->importBondCsv($csvData, $supplierId, $supplierName);
    }

    /**
     * Import tyres using FTP details from the supplier.
     */
    private function importFromFtp($ftpDetails, $supplierId, $supplierName)
    {
        // $this->testFtpConnection($ftpDetails);

        $fileContent = $this->fetchFileFromFtp($ftpDetails);
        $csvData = $this->parseCsv($fileContent);
        switch (strtolower($supplierName)) {
            case 'bond':
                $this->importBondCsv($csvData, $supplierId, $supplierName);
            break;
            case 'bits':
                $this->importBitsCsv($csvData, $supplierId, $supplierName);
            break;
            case 'etb':
                $this->importEtbCsv($csvData, $supplierId, $supplierName);
            break;
            case 'eden':
                    $this->importEdenCsv($csvData, $supplierId, $supplierName);
                break;
            case 'easityre':
                $this->importEasityreCsv($csvData, $supplierId, $supplierName);
            break;
            case 'kumho':
                $this->importKumhoCsv($csvData, $supplierId, $supplierName);
            break;
            case 'bmtr':
                $this->importBmtrCsv($csvData, $supplierId, $supplierName);
            break;
            // case 'ownstock':
            //     $this->importOwnStockCsv($csvData, $supplierId, $supplierName);
            // break;
            default:
                Log::warning('Unknown supplier name', ['supplier_name' => $supplierName]);
                throw new \Exception('Unknown supplier name: ' . $supplierName);
        }
        // Log::info('CSV data imported successfully', ['supplier_name' => $supplierName]);
        // Import the data
        // $this->importBondCsv($csvData, $supplierId, $supplierName);
    }

    /**
     * Import tyres from a specified file path (for "r" method).
     */
    private function importFromFilePath($filePath, $supplierId, $supplierName)
    {
        try {
            // Step 1: Log the file path being checked
            // Log::info('Checking file existence', ['file_path' => $filePath]);

            // Step 2: Check if the file exists
            if (!file_exists($filePath)) {
                // Log::error('File does not exist', ['file_path' => $filePath]);
                throw new \Exception('File does not exist at the specified path');
            }

            // Step 3: Get the file content
            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                // Log::error('Failed to read file content', ['file_path' => $filePath]);
                throw new \Exception('Failed to read file content');
            }

            // Step 4: Parse the CSV content
            // Log::info('Parsing CSV content', ['file_path' => $filePath]);
            $csvData = $this->parseCsv($fileContent);

            // Log the parsed CSV data
            // Log::info('CSV Data', ['csv_data' => $csvData]);

            if (empty($csvData)) {
                // Log::warning('Parsed CSV data is empty', ['file_path' => $filePath]);
                throw new \Exception('Parsed CSV data is empty or invalid');
            }

            // Step 5: Call the appropriate import function based on the supplier name
            // Log::info('Importing data for supplier', ['supplier_name' => $supplierName]);
            switch (strtolower($supplierName)) {
                case 'bond':
                    $this->importBondCsv($csvData, $supplierId, $supplierName);
                    break;
                case 'bits':
                    $this->importBitsCsv($csvData, $supplierId, $supplierName);
                break;
                case 'etb':
                    $this->importEtbCsv($csvData, $supplierId, $supplierName);
                break;
                 case 'eden':
                    $this->importEdenCsv($csvData, $supplierId, $supplierName);
                break;
                case 'easityre':
                    $this->importEasityreCsv($csvData, $supplierId, $supplierName);
                break;
                case 'bmtr':
                $this->importBmtrCsv($csvData, $supplierId, $supplierName);
                break;
                // case 'ownstock':
                //     $this->importOwnStockCsv($csvData, $supplierId, $supplierName);
                // break;
                default:
                    Log::warning('Unknown supplier name', ['supplier_name' => $supplierName]);
                    throw new \Exception('Unknown supplier name: ' . $supplierName);
            }

            // Step 6: Log success
            // Log::info('CSV data imported successfully', ['file_path' => $filePath, 'supplier_name' => $supplierName]);
        } catch (\Exception $e) {
            // Log and rethrow the exception for higher-level handling
            Log::error('Error during importFromFilePath', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function testFtpConnection(Request $request)
    {
        // Validate the input
        $request->validate([
            'ftp_host' => 'required|string',
            'ftp_user' => 'required|string',
            'ftp_password' => 'required|string',
        ]);

        try {
            // Attempt to connect to the FTP server using the provided details
            $this->ftpConnection->connect(
                $request->ftp_host,
                $request->ftp_user,
                $request->ftp_password
            );

            // Get the list of files in the root directory to verify the connection
            $files = $this->ftpConnection->getDirectoryList('/');

            // Successfully connected and retrieved files
            return response()->json([
                'success' => true,
                'message' => 'FTP connection successful!',
                'files' => $files,  // List of files to populate the directory dropdown
            ]);

        } catch (\Exception $e) {
            // If an error occurs, return the failure message
            return response()->json([
                'success' => false,
                'message' => 'FTP connection failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Fetch file from FTP server.
     */
    public function fetchFileFromFtp($ftpDetails)
    {
        // dd($ ftpDetails);
        $ftp_host = $ftpDetails['ftp_host'];
        $ftp_user = $ftpDetails['ftp_user'];
        $ftp_password = $ftpDetails['ftp_password'];
        $ftp_file_path = $ftpDetails['ftp_directory'];

        $ftp_connection = ftp_connect($ftp_host);
        if (!$ftp_connection) {
            throw new \Exception('Failed to connect to FTP server');
        }

        $login = ftp_login($ftp_connection, $ftp_user, $ftp_password);
        if (!$login) {
            ftp_close($ftp_connection);
            throw new \Exception('Failed to login to FTP server');
        }

        ftp_pasv($ftp_connection, true);

        $file_exists = ftp_size($ftp_connection, $ftp_file_path);
        // dd($file_exists);
        if ($file_exists == -1) {
            ftp_close($ftp_connection);
            throw new \Exception('File does not exist or cannot be accessed');
        }

        $local_file = storage_path('app/Bond.csv');
        $success = ftp_get($ftp_connection, $local_file, $ftp_file_path, FTP_BINARY);
        ftp_close($ftp_connection);

        if (!$success) {
            throw new \Exception('Failed to fetch the file');
        }

        return file_get_contents($local_file);
    }

    /**
     * Parse the CSV file content.
     */
    private function parseCsv($fileContent)
    {
        $lines = explode("\n", $fileContent);
        $csvData = [];

        foreach ($lines as $line) {
            if (empty($line))
                continue;
            $csvData[] = str_getcsv($line);
        }

        return $csvData;
    }
    private function normalizeKey($key)
    {
        return strtolower(str_replace([' ', '_', '-'], '', $key));
    }
    function generateRandom13DigitNumber()
    {
        return mt_rand(1000000000000, 9999999999999);
    }

    private function importBondCsv($fileContent, $supplierId, $supplierName)
    {
        set_time_limit(300);

        if (is_array($fileContent)) {
            $rows = $fileContent;
        } else {
            \Log::error('File content is not an array, unable to process.');
            return; 
        }

        $header = array_shift($rows);
        $header = array_map(function ($key) {
            return str_replace(' ', '_', $key);
        }, $header);

        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0) 
            ->delete();

        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }

        $insertData = [];
        $batchSize = 500;

        foreach ($rows as $line) {
            if (empty($line)) {
                continue;
            }

            $row = array_combine($header, $line);

            if (
                ($row['PRICE'] ?? 0) > 1 || ($row['STOCKBAL'] ?? 0) > 1 &&
                !empty($row['EAN']) && ($row['EAN'] != '-') &&
                !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM'])
            ) {

                $manufacturerId = null;
                $brandName = $row['BRAND'] ?? null;
                $budgettype= $row['BRAND_CATEGORY'] ?? null;

                if ($brandName) {
                    $brand = tyre_brands::where('name', '=', $brandName)->first();

                    if ($brand) {
                        $manufacturerId = $brand->brand_id;
                    } else {
                        $newBrandId = tyre_brands::insertGetId([
                            'name' => $brandName,
                            'slug' => Str::slug($brandName),
                            'promoted' => 0,
                            'image' => Str::slug($brandName) . '.webp',
                            'budget_type' => $budgettype,
                            'sort_order' => 1,
                            'status' => 1,
                            'product_type' => 'tyre',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        //dd($newBrandId);

                        $manufacturerId = $newBrandId;

                    }
                }

                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
                //dd($row); 
                
                $tyre_runflat = isset($row['RFT']) && ($row['RFT'] === 'Yes' || $row['RFT'] === 'RFT') ? 1 : 0;
                $tyre_extraload = isset($row['XL']) && $row['XL'] === 'XL' ? 1 : 0;
                /*$seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $tyre_season = isset($row['SEASON']) && isset($seasonMap[strtoupper($row['SEASON'])])
                    ? $seasonMap[strtoupper($row['SEASON'])]
                    : 'Summer';*/

                $tyre_season = $row['SEASON'] ?? null;
                $antiflat_text = $tyre_runflat ? 'RFT' : '';
                $reinforced_text = $tyre_extraload ? 'XL' : '';
                $season = $tyre_season ? $tyre_season . ' Tyre ' : 'Summer Tyre ';
                $rimText = $row['RIM'] ?? '';
                $vehicleType = strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? 'car')));
                if ($vehicleType == 'van' || $vehicleType == 'commercial van') {
                    $rimText .= 'C';
                }
                $additional_features = trim(($antiflat_text . ' ' . $reinforced_text));
                $tyre_data = [
                    'tyre_sku' => $row['BOND_CODE'] ?? null,
                    'tyre_ean' => $row['EAN'] ?? null,
                    'tyre_quantity' => ($row['STOCKBAL'] ?? 0) >= 1 ? $row['STOCKBAL'] : 0,
                    'tyre_price' => is_numeric($row['PRICE']) ? $row['PRICE'] : 0,
                    'tyre_brand_id' => $manufacturerId,
                    'tyre_season' => $tyre_season,
                    'tyre_width' => $row['SECTION'] ?? null,
                    'tyre_profile' => $row['PROFILE'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'tyre_loadindex' => $row['LOAD_INDEX'] ?? null,
                    'status' => ($row['STOCKBAL'] ?? 0) >= 1 ? 1 : 0,
                    'tyre_fullyfitted_price' => (float) ($row['PRICE'] ?? 0) + 25,
                    'trade_costprice' => (float) ($row['PRICE'] ?? 0) + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['PATTERN'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tyre_noisedb' => is_numeric(
                            $normalized_row['noise'] 
                            ?? $normalized_row['noisedb'] 
                            ?? $normalized_row['noise_'] 
                            ?? null
                        )
                        ? (
                            $normalized_row['noise'] 
                            ?? $normalized_row['noisedb'] 
                            ?? $normalized_row['noise_']
                        )
                        : null,

                    //'tyre_fuel' => $row['FUEL'] ?? null,
                        'tyre_fuel' => isset($row['FUEL']) 
                            ? strtoupper(substr(str_replace('-', '', trim($row['FUEL'])), 0, 1)) 
                            : null,

                                            //'tyre_wetgrip' => $row['WET'] ?? '',
                            'tyre_wetgrip' => isset($row['WET']) 
                            ? strtoupper(substr(str_replace('-', '', trim($row['WET'])), 0, 1)) 
                            : null,

                    'tyre_runflat' => $tyre_runflat,
                    
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' =>  $vehicleType,
                    'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'date_available' => now(),
                    'tyre_image' => $row['IMAGE'] ?? null,
                    'supplier_id' => $supplierId,  // Add supplier_id here
                    'tyre_supplier_name' => $supplierName,
                    'tyre_description' => trim(
                        $season . '' .
                        ($brandName ?? '') . ' ' .
                        ($row['PATTERN'] ?? '') . ' ' .
                        ($row['SECTION'] ?? '') . '/' .
                        ($row['PROFILE'] ?? '') . 'R' .
                        $rimText . ' ' .
                        ($row['LOAD_INDEX'] ?? '') . ' ' .
                        ($row['SPEED'] ?? '') . ' ' .
                        $additional_features
                    ),

                ];
                //dd($tyre_data);

                $insertData[] = $tyre_data;

                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }

        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }

    private function importBitsCsv($fileContent, $supplierId, $supplierName)
    {
        // Set a longer execution time to handle large data imports
        set_time_limit(300);

        // Ensure $fileContent is an array of rows
        if (!is_array($fileContent)) {
            \Log::error('File content is not an array, unable to process.');
            return; // Exit if file content is not in the expected format
        }

        // Step 1: Extract the header (first row)
        $header = array_shift($fileContent); // Remove the first row as the header

        // Step 2: Delete existing data for the given supplier and source
        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();

        // Step 3: Reset AUTO_INCREMENT if the table is empty
        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }

        // Step 4: Batch processing variables
        $insertData = [];
        $batchSize = 500;

        // Helper function to normalize keys
        // Step 5: Process each row in the CSV file
        foreach ($fileContent as $line) {
            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Combine header and line into an associative array
            $row = array_combine($header, $line);

            // Apply your conditions for valid rows
            if (
                ($row['PRICE'] ?? 0) > 1 || ($row['STOCKBAL'] ?? 0) > 1 &&
                !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM'])
            ) {
                // Handle manufacturer_id logic
                $manufacturerId = null;
                $brandName = $row['BRAND'] ?? null;

                if ($brandName) {
                    // Check if the brand exists
                    $brand = tyre_brands::where('name', '=', $brandName)->first();

                    if ($brand) {
                        $manufacturerId = $brand->brand_id;
                    } else {
                        // Create a new brand entry
                        $manufacturerId = tyre_brands::insertGetId([
                            'name' => $brandName,
                            'slug' => Str::slug($brandName),
                            'promoted' => 0,
                            'image' => Str::slug($brandName) . '.jpg',
                            'sort_order' => 1,
                            'status' => 1,
                            'product_type' => 'tyre',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Normalize the row keys
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
                // Generate SKU and EAN if not provided
                $sku = $row['SKU'] ?? $this->generateRandom13DigitNumber() . 'A';
                $ean = $row['EAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
                $tyre_runflat = isset($row['RFT']) && ($row['RFT'] === 'Yes' || $row['RFT'] === 'RFT') ? 1 : 0;
                $tyre_extraload = isset($row['XL']) && $row['XL'] === 'XL' ? 1 : 0;
                $seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $tyre_season = isset($row['SEASON']) && isset($seasonMap[strtoupper($row['SEASON'])])
                    ? $seasonMap[strtoupper($row['SEASON'])]
                    : 'Summer';

                // Handle human-readable text for antiflat and reinforcement
                $antiflat_text = $tyre_runflat ? 'RFT' : '';
                $reinforced_text = $tyre_extraload ? 'XL' : '';
                $season = $tyre_season ? $tyre_season . ' Tyre ' : 'Summer Tyre ';

                // Combine texts intelligently for the description
                $additional_features = trim(($antiflat_text . ' ' . $reinforced_text));
                $rimText = $row['RIM'] ?? '';
                // if ($row['VEHICLE_TYPE'] === 'van' || $row['VEHICLE_TYPE'] === 'commercial van') {
                //     $rimText .= 'C';
                // }
                // Prepare the tyre data and include supplier_id and supplier_name
                $tyre_data = [
                    'tyre_sku' => $sku,
                    'tyre_ean' => $ean,
                    'tyre_quantity' => is_numeric($row['STOCKBAL'] ?? 0) >= 1 ? (int) $row['STOCKBAL'] : 0,
                    'tyre_price' => $row['COST_PRICE'] ?? 0,
                    'tyre_brand_id' => $manufacturerId,
                    'tyre_season' => $tyre_season,
                    'tyre_width' => $row['SECTION'] ?? null,
                    'tyre_profile' => (isset($row['PROFILE']) && (strlen($row['PROFILE']) == 2 || strlen($row['PROFILE']) == 3))
                        ? $row['PROFILE']
                        : null,
                    'tyre_loadindex' => $row['LOAD_INDEX'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'status' => ($row['STOCKBAL'] ?? 0) >= 1 ? 1 : 0,
                    'tyre_fullyfitted_price' => $row['COST_PRICE'] + 25,
                    'trade_costprice' => $row['COST_PRICE'] + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['PATTERN'] ?? null,
                    'tyre_noisedb' => is_numeric($normalized_row['noise'] ?? $normalized_row['noisedb'] ?? null)
                        ? ($normalized_row['noise'] ?? $normalized_row['noisedb'])
                        : null,
                    'tyre_fuel' => $row['FUEL'] ?? null,
                    'tyre_wetgrip' => $row['WET'] ?? '',
                    'tyre_runflat' => $tyre_runflat,
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? 'car'))),
                    'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'lead_time' => $row['LEAD_TIME'] ?? $row['LEAD_TIME'] ?? 'Available Today',
                    'date_available' => now(),
                    'tyre_image' => $row['IMAGE'] ?? null,
                    'supplier_id' => $supplierId,
                    'tyre_supplier_name' => $supplierName,
                    'created_at' => now(),
                    'updated_at' => now(),

                    // Construct the description

                    // ... other fields ...
                    'tyre_description' => trim(
                        $season . '' .
                        ($brandName ?? '') . ' ' .
                        ($row['PATTERN'] ?? '') . ' ' .
                        ($row['SECTION'] ?? '') . '/' .
                        ($row['PROFILE'] ?? '') . 'R' .
                        $rimText . ' ' .
                        ($row['LOAD_INDEX'] ?? '') . ' ' .
                        ($row['SPEED'] ?? '') . ' ' .
                        $additional_features
                    ),

                ];

                // Add the processed data to the insert array
                $insertData[] = $tyre_data;
                // dd($insertData);
                // Insert data in batches for efficiency
                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }

        // Insert any remaining data that was not inserted in the batch
        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }
    private function importKumhoCsv($fileContent, $supplierId, $supplierName)
    {
        // dd($fileContent);
        // Set a longer execution time to handle large data imports
        set_time_limit(300);

        // Ensure $fileContent is an array of rows
        if (!is_array($fileContent)) {
            \Log::error('File content is not an array, unable to process.');
            return; // Exit if file content is not in the expected format
        }

        // Step 1: Extract the header (first row)
        $header = array_shift($fileContent); // Remove the first row as the header

        // Step 2: Delete existing data for the given supplier and source
        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();

        // Step 3: Reset AUTO_INCREMENT if the table is empty
        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }

        // Step 4: Batch processing variables
        $insertData = [];
        $batchSize = 500;

        // Helper function to normalize keys
        // Step 5: Process each row in the CSV file
        foreach ($fileContent as $line) {
            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Combine header and line into an associative array
            $row = array_combine($header, $line);

            // Apply your conditions for valid rows
            if (
                ($row['PRICE'] ?? 0) > 1 || ($row['STOCKBAL'] ?? 0) > 1 &&
                !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM'])
            ) {
                // Handle manufacturer_id logic
                $manufacturerId = null;
                $brandName = $row['BRAND'] ?? null;

                if ($brandName) {
                    // Check if the brand exists
                    $brand = tyre_brands::where('name', '=', $brandName)->first();

                    if ($brand) {
                        $manufacturerId = $brand->brand_id;
                    } else {
                        // Create a new brand entry
                        $manufacturerId = tyre_brands::insertGetId([
                            'name' => $brandName,
                            'slug' => Str::slug($brandName),
                            'promoted' => 0,
                            'image' => Str::slug($brandName) . '.jpg',
                            'sort_order' => 1,
                            'status' => 1,
                            'product_type' => 'tyre',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Normalize the row keys
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
                // Generate SKU and EAN if not provided
                $sku = $row['SKU'] ?? $this->generateRandom13DigitNumber() . 'A';
                $ean = $row['EAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
                $tyre_runflat = isset($row['RFT']) && ($row['RFT'] === 'Yes' || $row['RFT'] === 'RFT') ? 1 : 0;
                $tyre_extraload = isset($row['XL']) && $row['XL'] === 'XL' ? 1 : 0;
                $seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $tyre_season = isset($row['SEASON']) && isset($seasonMap[strtoupper($row['SEASON'])])
                    ? $seasonMap[strtoupper($row['SEASON'])]
                    : 'Summer';

                // Handle human-readable text for antiflat and reinforcement
                $antiflat_text = $tyre_runflat ? 'RFT' : '';
                $reinforced_text = $tyre_extraload ? 'XL' : '';
                $season = $tyre_season ? $tyre_season . ' Tyre ' : 'Summer Tyre ';

                // Combine texts intelligently for the description
                $additional_features = trim(($antiflat_text . ' ' . $reinforced_text));
                $rimText = $row['RIM'] ?? '';
                if ($row['VEHICLE_TYPE'] === 'van' || $row['VEHICLE_TYPE'] === 'commercial van') {
                    $rimText .= 'C';
                }
                // Prepare the tyre data and include supplier_id and supplier_name
                $tyre_data = [
                    'tyre_sku' => $sku,
                    'tyre_ean' => $ean,
                    'tyre_quantity' => is_numeric($row['STOCKBAL'] ?? 0) >= 1 ? (int) $row['STOCKBAL'] : 0,
                    'tyre_price' => $row['COST_PRICE'] ?? 0,
                    'tyre_brand_id' => $manufacturerId,
                    'tyre_season' => $tyre_season,
                    'tyre_width' => $row['SECTION'] ?? null,
                    'tyre_profile' => (isset($row['PROFILE']) && (strlen($row['PROFILE']) == 2 || strlen($row['PROFILE']) == 3))
                        ? $row['PROFILE']
                        : null,
                    'tyre_loadindex' => $row['LOAD_INDEX'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'status' => ($row['STOCKBAL'] ?? 0) >= 1 ? 1 : 0,
                    'tyre_fullyfitted_price' => $row['COST_PRICE'] + 25,
                    'trade_costprice' => $row['COST_PRICE'] + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['PATTERN'] ?? null,
                    'tyre_noisedb' => is_numeric($normalized_row['noise'] ?? $normalized_row['noisedb'] ?? null)
                        ? ($normalized_row['noise'] ?? $normalized_row['noisedb'])
                        : null,
                    'tyre_fuel' => $row['FUEL'] ?? null,
                    'tyre_wetgrip' => $row['WET'] ?? '',
                    'tyre_runflat' => $tyre_runflat,
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? 'car'))),
                    'weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'lead_time' => $row['LEAD_TIME'] ?? $row['LEAD_TIME'] ?? 'Available Today',
                    'date_available' => now(),
                    'tyre_image' => $row['IMAGE'] ?? null,
                    'supplier_id' => $supplierId,
                    'tyre_supplier_name' => $supplierName,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tyre_description' => trim(
                        $season . '' .
                        ($brandName ?? '') . ' ' .
                        ($row['PATTERN'] ?? '') . ' ' .
                        ($row['SECTION'] ?? '') . '/' .
                        ($row['PROFILE'] ?? '') . 'R' .
                        $rimText . ' ' .
                        ($row['LOAD_INDEX'] ?? '') . ' ' .
                        ($row['SPEED'] ?? '') . ' ' .
                        $additional_features
                    ),

                ];

                // Add the processed data to the insert array
                $insertData[] = $tyre_data;
                // dd($insertData);
                // Insert data in batches for efficiency
                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }

        // Insert any remaining data that was not inserted in the batch
        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }
    private function importEtbCsv($fileContent, $supplierId, $supplierName)
    {
        // Set a longer execution time to handle large data imports
        set_time_limit(300);
    
        // Ensure $fileContent is an array of rows
        if (!is_array($fileContent)) {
            \Log::error('File content is not an array, unable to process.');
            return; // Exit if file content is not in the expected format
        }
    
        // Manufacturer mapping
        $cm = [
            'CO'=>'CONTINENTAL','FI'=>'Firestone','LI'=>'LingLong','94'=>'RoadX','CI' => 'CITYST','76'  => 'Mileking','VR'=>'Vredestein','HI'=>'Hifly','61'=>'SAILWIN','LS'=>'LANDSAIL','CA'=>'Camac','AY'=>'Aptany','BR'=>'BRIDGESTONE','A9'=>'APLUS','DV'=>'Davanti','EG'=>'EVERGREEN','EV'=>'EVENT','G8'=>'Grenlander','GO'=>'GOODYEAR','SA'=>'Sava','HA'=>'HANKOOK','Z1'=>'Antares','DU'=>'DUNLOP','LN'=>'Lanvigator','NA'=>'NANKANG','AV'=>'AVON','LA'=>'LASSA','NE'=>'NEXEN','RA'=>'Rapid','FA'=>'FALKEN','MI'=>'MICHELIN','C8'=>'CONSTANCY','D9'=>'Duraturn','TO'=>'TOYO','C0'=>'Comforser','KR'=>'KETER','RI'=>'RIKEN','NN'=>'NEOLIN ','PI'=>'pirelli','OV'=>'Ovation','HD'=>'HAIDA','GE'=>'GENERAL','R5'=>'Roadstone','YO'=>'YOKOHAMA','IN'=>'Infinity','PA'=>'PACE','EP'=>'Accelera','KU'=>'Kumho','GD'=>'GOODRIDE','SY'=>'SUNNY','JR'=>'Joyroad','MR'=>'MARSHAL','AG'=>'Autogrip','S4'=>'Saferich','WN'=>'WANLI','UG'=>'Unigrip','E3'=>'EXCELON','MX'=>'MAXXIS','WE'=>'westlake','ZA'=>'ZETA','FR'=>'Fullrun','KO'=>'Kormoran','TG'=>'Triangle','B1'=>'BALKAR','T0'=>'TRAZANO','A2'=>'Autogreen','M4'=>'MINNAL','P0'=>'POWERTRAC','M2'=>'MATRIC','80'=>'RCBLDE','MG'=>'MIRAGE','GS'=>'GOALST','KN'=>'KENDA','RB'=>'ROYAL','M1'=>'MAZZINI','RO'=>'ROTALLA','DL'=>'DELINTE','G1'=>'GOVIND','UN'=>'UNIROYAL','W1'=>'WINRUN','RY'=>'RYDANZ','BL'=>'BLACKLION','IM'=>'IMPERIAL','FW'=>'FULLWAY','S6'=>'SUPERIA','T5'=>'TRISTAR','JI'=>'JINYU','MV'=>'MINERVA','FO'=>'FORTUNA','RS'=>'ROADSTONE','IO'=>'NITTO INVO','CR'=>'COOPER','C7'=>'CST','WF'=>'WINDFORCE','GW'=>'GOLDWAY','SC'=>'SECURITY','D1'=>'DURUN','MK'=>'MAXTREK','RU'=>'RUNWAY','DN'=>'DEESTONE','S0'=>'STARCO','37'=>'RST','GT'=>'GITI','BF'=>'BFGOODRICH','86'=>'MICKY THOMPSON','AT'=>'ATTURO','GF'=>'GOFORM','BB'=>'Boristar','ZY'=>'Z-TYRE','C3'=>'CENTARA','SL'=>'SAILUN','P6'=>'Protector','AO'=>'APOLLO','IL'=>'ILINK','DX'=>'DEXTERO','CE'=>'CEAT','AM'=>'ARMSTNG','IS'=>'INSTATE','AI'=>'AOTELI','FX'=>'FIREMX','K2'=>'KPATOS','VT'=>'VITORA','K1'=>'KNGRUN','ZX'=>'ZMAX','ZE'=>'ZEETEX','KT'=>'KNGSTAR','QE'=>'GAZELL','YT'=>'YATONE','DY'=>'DYNAMO','R8'=>'RDMRCH','RD'=>'RADAR','TX'=>'TRCMAX','FP'=>'FARRIDE','C2'=>'CHURCH','A8'=>'ATLS','PE'=>'PETLS','CQ'=>'COMSAL','D2'=>'DELI','DR'=>'DURO','JO'=>'JOURNE','H1'=>'HILO',
         ];
    
        // Step 1: Extract the header (first row)
        $header = array_shift($fileContent); // Remove the first row as the header
    
        // Step 2: Delete existing data for the given supplier and source
        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();
    
        // Step 3: Reset AUTO_INCREMENT if the table is empty
        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }
        // Step 4: Batch processing variables
        $insertData = [];
        $batchSize = 500;
    
        // Helper function to normalize keys
        // Step 5: Process each row in the CSV file
        foreach ($fileContent as $line) {
            // Skip empty lines
            if (empty($line)) {
                continue;
            }
    
            // Combine header and line into an associative array
            $row = array_combine($header, $line);
    
            // Apply your conditions for valid rows
            if (
                ($row['UNITCOST'] ?? 0) > 1 && ($row['QUANTITY'] ?? 0) > 1 &&
                !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM'] && (!empty($row['TYNOISEDB']) && $row['TYNOISEDB'] > 0))
            ) {
                // Handle manufacturer_id logic using the mapping array
                $manufacturerId = null;
                $brandCode = $row['MANUFCTR'] ?? null;
                $brandName = isset($cm[$brandCode]) ? $cm[$brandCode] : null;
    
                if ($brandName) {
                    // Check if the brand exists
                    $brand = tyre_brands::where('name', '=', $brandName)->first();
                    if ($brand) {
                        $manufacturerId = $brand->brand_id;
                    } else {
                        // Create a new brand entry
                        try {
                            $manufacturerId = tyre_brands::insertGetId([
                                'name' => $brandName,
                                'slug' => Str::slug($brandName),
                                'promoted' => 0,
                                'image' => Str::slug($brandName) . '.jpg',
                                'sort_order' => 1,
                                'status' => 1,
                                'product_type' => 'tyre',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to create tyre brand: ' . $brandName);
                            continue; // Skip this row if brand creation fails
                        }
                    }
                } else {
                    \Log::warning('Manufacturer code not found in mapping: ' . $brandCode);
                    continue; // Skip this row if manufacturer code is invalid
                }
    
                // Normalize the row keys
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
    
                // Generate SKU and EAN if not provided
                $sku = $row['STCODE'] ?? $this->generateRandom13DigitNumber() . 'A';
                $ean = $row['STEAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
    
                // Stock and quantity logic
                if (($row['QUANTITY'] ?? 0) > 1) {
                    $quantity = $row['QUANTITY'];
                    $stock_status_id = 6; // In stock
                    $status = 1;
                } else {
                    $quantity = 0;
                    $stock_status_id = 5; // Out of stock
                    $status = 0;
                }
    
                // Tyre properties
                $tyre_runflat = isset($row['RUNFLAT']) && $row['RUNFLAT'] === 'T' ? 1 : 0;
                $tyre_extraload = isset($row['EXLOAD']) && $row['EXLOAD'] === 'T' ? 1 : 0;
                $seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $tyre_season = isset($row['TYSEASON']) && isset($seasonMap[strtoupper($row['TYSEASON'])])
                    ? $seasonMap[strtoupper($row['TYSEASON'])]
                    : 'Summer';
                // $tyre_season = $row['TYSEASON'] ?? 'Summer'; // Default to 'Summer' if not specified
    
                // Validate and normalize tyre_width
                $tyre_width = preg_replace('/[^0-9]/', '', $row['SECTION'] ?? ''); // Strip non-numeric characters
                if (strlen($tyre_width) > 10) { // Log a warning if the width exceeds expected length
                    \Log::warning('Invalid tyre_width value: ' . $row['SECTION']);
                    $tyre_width = substr($tyre_width, 0, 10); // Truncate to 10 characters
                }
                $tyre_profile = preg_replace('/[^0-9]/', '', $row['PROFILE'] ?? ''); // Strip non-numeric characters
                if (strlen($tyre_profile) > 10) { // Log a warning if the profile exceeds expected length
                    \Log::warning('Invalid tyre_profile value: ' . $row['PROFILE']);
                    $tyre_profile = substr($tyre_profile, 0, 10); // Truncate to 10 characters
                }
                // Prepare the tyre data
                $tyre_data = [
                    'tyre_sku' => $sku,
                    'tyre_ean' => $ean,
                    'tyre_quantity' => (int)$quantity,
                    'tyre_price' => $row['UNITCOST'] ?? 0,
                    'tyre_brand_id' => $manufacturerId,
                    'tyre_season' => $tyre_season,
                    'tyre_width' => $tyre_width, // Use normalized value
                    'tyre_profile' => $tyre_profile,
                    'tyre_loadindex' => $row['LOADIX'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'status' => $status,
                    'tyre_fullyfitted_price' => ($row['UNITCOST'] ?? 0) + 25,
                    'trade_costprice' => ($row['UNITCOST'] ?? 0) + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['TREADPAT'] ?? null,
                    'tyre_noisedb' => (!empty($row['TYNOISEDB']) && $row['TYNOISEDB'] > 0) ? $row['TYNOISEDB'] : null,
                    'tyre_fuel' => $row['TYFUELC'] ?? null,
                    'tyre_wetgrip' => $row['TYWGC'] ?? null,
                    'tyre_runflat' => $tyre_runflat,
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? 'car'))),
                    'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'lead_time' => $row['LEAD_TIME'] ?? null,
                    'date_available' => now(),
                    'tyre_image' => $row['IMAGE'] ?? null,
                    'supplier_id' => $supplierId,
                    'tyre_supplier_name' => $supplierName,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tyre_description' => trim(
                        $tyre_season . ' Tyre ' .
                        ($brandName ?? '') . ' ' .
                        ($row['TREADPAT'] ?? '') . ' ' .
                        ($tyre_width ?? '') . '/' .
                        ($tyre_profile ?? '') . 'R' .
                        ($row['RIM'] ?? '') . ' ' .
                        ($row['LOADIX'] ?? '') . ' ' .
                        ($row['SPEED'] ?? '') . ' ' .
                        ($tyre_runflat ? 'RFT' : '') . ' ' .
                        ($tyre_extraload ? 'XL' : '')
                    ),
                ];
    
                // Add the processed data to the insert array
                $insertData[] = $tyre_data;
    
                // Insert data in batches for efficiency
                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }
    
        // Insert any remaining data that was not inserted in the batch
        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }

        private function importEdenCsv($fileContent, $supplierId, $supplierName)
    {
        set_time_limit(300);
    
        if (!is_array($fileContent) || count($fileContent) < 2) {
            \Log::error('File content invalid or too short.');
            return;
        }
    
       $header = array_shift($fileContent); // Remove the first row as the header
        $header = array_map(function($key) {
            return str_replace(' ', '_', $key);
        }, $header);

        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();
    
        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }
    
        $insertData = [];
        $batchSize = 500;
 
        foreach ($fileContent as $line) {
            if (empty($line)) {
                continue;
            }
    
            $row = array_combine($header, $line);
           
            if (
                    ($row['Price'] ?? 0) > 1 &&
                    ($row['Product_Available'] ?? 0) > 1 &&
                    !empty($row['Width']) &&
                    !empty($row['Aspect_Ratio']) &&
                    !empty($row['Rim']) &&
                    isset($row['Noise_Performance']) &&
                    is_numeric($row['Noise_Performance']) &&
                    $row['Noise_Performance'] > 0
                ) {


                 
                $brandId = null;
                $brandCode = $row['Brand_Name'] ?? null;
                $brandName = isset($brandCode) ? $brandCode : null;
    
                if ($brandName) {
                    $brand = tyre_brands::where('name', '=', $brandName)->first();
                    if ($brand) {
                        $brandId = $brand->brand_id;
                    } else {
                        try {
                            $brandId = tyre_brands::insertGetId([
                                'name' => $brandName,
                                'slug' => Str::slug($brandName),
                                'promoted' => 0,
                                'tyre_image' => Str::slug($brandName) . '.jpg',
                                'sort_order' => 1,
                                'status' => 1,
                                'product_type' => 'tyre',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to create tyre brand: ' . $brandName);
                            continue; 
                        }
                    }
                } else {
                    \Log::warning('Manufacturer code not found in mapping: ' . $brandCode);
                    continue; 
                }
    
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
    //dd($row);
                $sku = $row['Product_Stock_Number'] ?? $this->generateRandom13DigitNumber() . 'A';
                $ean = $row['Product_EAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
    
                if (($row['Product_Available'] ?? 0) > 1) {
                    $quantity = $row['Product_Available'];
                    $status = 1;
                } else {
                    $quantity = 0;
                    $status = 0;
                }
    
                $tyre_runflat = isset($row['Runflat']) && $row['Runflat'] === 'TRUE' ? 1 : 0;
                $tyre_extraload = isset($row['Reinforced']) && $row['Reinforced'] === 'XL' ? 1 : 0;
               
                if (stripos($row['Product_Title'], 'CAR ') !== false) {
                    $tyre_vehicle_type = 'car';
                } elseif (stripos($row['Product_Title'], '4X4 / SUV') !== false) {
                    $tyre_vehicle_type = '4x4';
                } elseif (stripos($row['Product_Title'], 'VAN ') !== false) {
                    $tyre_vehicle_type = 'van';
                }else{
                    $tyre_vehicle_type = 'car';
                }

                $tyre_season = 'Summer'; // default
                if (stripos($row['Product_Title'], 'ALL SEASON') !== false) {
                    $tyre_season = 'All Season';
                } elseif (stripos($row['Product_Title'], 'WINTER') !== false) {
                    $tyre_season = 'Winter';
                } elseif (stripos($row['Product_Title'], 'SUMMER') !== false) {
                    $tyre_season = 'Summer';
                }

                $tyre_data = [
                    'tyre_sku' => $sku,
                    'tyre_ean' => $ean,
                    'tyre_quantity' => (int)$quantity,
                    'tyre_price' => $row['Price'],
                    'tyre_brand_id' => $brandId,
                    'tyre_season' => $tyre_season ?? null,
                    'tyre_width' => $row['Width'] ?? null,
                    'tyre_profile' => $row['Aspect_Ratio'] ?? null,
                    'tyre_diameter' => $row['Rim'] ?? null,
                    'tyre_speed' => $row['Speed_Rating'] ?? null,
                    'tyre_loadindex' => $row['Load_Index'] ?? null,
                    'status' => $status,
                    'tyre_fullyfitted_price' => $row['Price'],
                    'trade_costprice' => ($row['Price'] ?? 0) + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['Model_Name'] ?? null,
                    'tyre_fuel' => $row['Rolling_Resistance'] ?? null,
                    'tyre_wetgrip' => $row['Wet_Grip'] ?? null,
                    'tyre_noisedb' =>$row['Noise_Performance'] ?? null,
                    'tyre_runflat' => $tyre_runflat,
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' => $tyre_vehicle_type,
                    'tyre_weight' => '',
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'lead_time' => '',
                    'date_available' => now(),
                    'tyre_image' => '',
                    
                    'supplier_id' => $supplierId,
                    'tyre_supplier_name' => $supplierName,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tyre_description' => trim(
                        $tyre_season . ' Tyre ' .
                        ($brandName ?? '') . ' ' .
                        ($row['Model_Name'] ?? '') . ' ' .
                        ($row['Width'] ?? '') . '/' .
                        ($row['Aspect_Ratio'] ?? '') . 'R' .
                        ($row['Rim'] ?? '') . ' ' .
                        ($row['Load_Index'] ?? '') . ' ' .
                        ($row['Speed_Rating'] ?? '') . ' ' .
                        ($tyre_runflat ? 'RFT' : '') . ' ' .
                        ($tyre_extraload ? 'XL' : '')
                    ),
                ];
                //dd($tyre_data);
                $insertData[] = $tyre_data;
            
                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }

        if (!empty($insertData)) {
            \Log::info('Inserting ' . count($insertData) . ' products for supplier ' . $supplierName);
            DB::table('tyres_product')->insert($insertData);
        }
    
        
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }

    private function importEasityreCsv($fileContent, $supplierId, $supplierName)
    {
        set_time_limit(300);
        if (!is_array($fileContent)) {
            \Log::error('File content is not an array, unable to process.');
            return; // Exit if file content is not in the expected format
        }
        $header = array_shift($fileContent); // Remove the first row as the header
        $header = array_map(function($key) {
            return str_replace(' ', '_', $key);
        }, $header);
        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();

        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }

        $insertData = [];
        $batchSize = 500;

        foreach ($fileContent as $line) {
            if (empty($line)) {
                continue;
            }

            $row = array_combine($header, $line);
            /*if (
                ($row['Price'] ?? 0) > 1 || ($row['Product_Available'] ?? 0) > 1 &&
                !empty($row['Width']) && !empty($row['Aspect_Ratio']) && !empty($row['Rim'])
            ) {*/
            if (( ($row['Price'] ?? 0) > 1 || ($row['Product_Available'] ?? 0) > 1) && !empty($row['Width']) && !empty($row['Aspect_Ratio']) && !empty($row['Rim']) && !empty($row['Product_EAN']) && trim($row['Rolling_Resistance'] ?? '') !== '' && trim($row['Wet_Grip'] ?? '') !== '' && ($row['Noise_Performance'] ?? 0) > 2 ){
                $manufacturerId = null;
                $brandName = $row['Brand_Name'] ?? null;

            if ($brandName) {
                $brand = tyre_brands::where('name', '=', $brandName)->first();
                if ($brand) {
                    $manufacturerId = $brand->brand_id;
                } else {
                    $manufacturerId = tyre_brands::insertGetId([
                        'name' => $brandName,
                        'slug' => Str::slug($brandName),
                        'promoted' => 0,
                        'image' => Str::slug($brandName) . '.jpg',
                        'sort_order' => 1,
                        'status' => 1,
                        'product_type' => 'tyre',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $normalized_row = [];
            foreach ($row as $key => $value) {
                $normalized_row[$this->normalizeKey($key)] = $value;
            }
            $sku = $row['Product_Stock_Number'] ?? $this->generateRandom13DigitNumber() . 'A';
            $ean = $row['Product_EAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
            $tyre_runflat = isset($row['Runflat']) && ($row['Runflat'] === 'TRUE') ? 1 : 0;
            $tyre_extraload = isset($row['Reinforced']) && $row['Reinforced'] === 'XL' ? 1 : 0;
            $tyre_season = isset($row['Product_Type']) ? ucwords(strtolower(trim($row['Product_Type']))) : null;
            $antiflat_text = $tyre_runflat ? 'RFT' : '';
            $reinforced_text = $tyre_extraload ? 'XL' : '';
            $season = $tyre_season ? $tyre_season . ' Tyre ' : 'Summer Tyre ';
            $additional_features = trim(($antiflat_text . ' ' . $reinforced_text));
            $rimText = $row['RIM'] ?? '';
            if ($row['Vehicle_Type'] === 'van' || $row['Vehicle_Type'] === 'commercial van') {
                $rimText .= 'C';
            }
            $tyre_data = [
                'tyre_sku' => $sku,
                'tyre_ean' => $ean,
                'tyre_quantity' => is_numeric($row['Product_Available'] ?? 0) >= 1 ? (int) $row['Product_Available'] : 0,
                'tyre_price' => $row['Price'] ?? 0,
                'tyre_brand_id' => $manufacturerId,
                'tyre_season' => $tyre_season,
                'tyre_width' => $row['Width'] ?? null,
                'tyre_profile' => (isset($row['Aspect_Ratio']) && (strlen($row['Aspect_Ratio']) == 2 || strlen($row['Aspect_Ratio']) == 3))
                    ? $row['Aspect_Ratio']
                    : null,
                'tyre_loadindex' => $row['Load_Index'] ?? null,
                'tyre_diameter' => $row['Rim'] ?? null,
                'tyre_speed' => $row['Speed_Rating'] ?? null,
                'status' => ($row['Product_Available'] ?? 0) >= 1 ? 1 : 0,
                'tyre_fullyfitted_price' => $row['Price'],
                'trade_costprice' => $row['Price'] + 4.84,
                'tyre_brand_name' => $brandName,
                'tyre_model' => $row['Model_Name'] ?? null,
                'tyre_noisedb' => isset($row['Noise_Performance']) ? $row['Noise_Performance'] : null,
                //'tyre_fuel' => $row['Rolling_Resistance'] ?? null,
                'tyre_fuel' => isset($row['Rolling_Resistance']) ? substr($row['Rolling_Resistance'], 0, 1) : null,
                'tyre_wetgrip' => isset($row['Wet_Grip']) ? substr($row['Wet_Grip'], 0, 1) : null,
                'tyre_runflat' => $tyre_runflat,
                'tyre_extraload' => $tyre_extraload,
                'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['Vehicle_Type'] ?? 'car'))),
                'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                'product_type' => 'tyre',
                'tax_class_id' => 9,
                'lead_time' => $row['LEAD_TIME'] ?? $row['LEAD_TIME'] ?? 'Available Today',
                'date_available' => now(),
                'tyre_image' => $row['IMAGE'] ?? null,
                'supplier_id' => $supplierId,
                'tyre_supplier_name' => $supplierName,
                'created_at' => now(),
                'updated_at' => now(),
                'tyre_description' => trim(
                    $season . '' .
                    ($brandName ?? '') . ' ' .
                    ($row['Model_Name'] ?? '') . ' ' .
                    ($row['Width'] ?? '') . '/' .
                    ($row['Aspect_Ratio'] ?? '') . 'R' .
                    $rimText . ' ' .
                    ($row['Load_Index'] ?? '') . ' ' .
                    ($row['Speed_Rating'] ?? '') . ' ' .
                    $additional_features
                ),
            ];

            $insertData[] = $tyre_data;
            if (count($insertData) >= $batchSize) {
                TyresProduct::insert($insertData);
                $insertData = [];
            }
            }
        }

        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }
    private function importBmtrCsv($fileContent, $supplierId, $supplierName)
    {
        set_time_limit(300);
    
        if (!is_array($fileContent)) {
            \Log::error('File content invalid or too short.');
            return;
        }

        // Manufacturer mapping
        $cm = [
            'CO'=>'CONTINENTAL','FI'=>'Firestone','LI'=>'LingLong','94'=>'RoadX','CI' => 'CITYST','76'  => 'Mileking','VR'=>'Vredestein','HI'=>'Hifly','61'=>'SAILWIN','LS'=>'LANDSAIL','CA'=>'Camac','AY'=>'Aptany','BR'=>'BRIDGESTONE','A9'=>'APLUS','DV'=>'Davanti','EG'=>'EVERGREEN','EV'=>'EVENT','G8'=>'Grenlander','GO'=>'GOODYEAR','SA'=>'Sava','HA'=>'HANKOOK','Z1'=>'Antares','DU'=>'DUNLOP','LN'=>'Lanvigator','NA'=>'NANKANG','AV'=>'AVON','LA'=>'LASSA','NE'=>'NEXEN','RA'=>'Rapid','FA'=>'FALKEN','MI'=>'MICHELIN','C8'=>'CONSTANCY','D9'=>'Duraturn','TO'=>'TOYO','C0'=>'Comforser','KR'=>'KETER','RI'=>'RIKEN','NN'=>'NEOLIN ','PI'=>'pirelli','OV'=>'Ovation','HD'=>'HAIDA','GE'=>'GENERAL','R5'=>'Roadstone','YO'=>'YOKOHAMA','IN'=>'Infinity','PA'=>'PACE','EP'=>'Accelera','KU'=>'Kumho','GD'=>'GOODRIDE','SY'=>'SUNNY','JR'=>'Joyroad','MR'=>'MARSHAL','AG'=>'Autogrip','S4'=>'Saferich','WN'=>'WANLI','UG'=>'Unigrip','E3'=>'EXCELON','MX'=>'MAXXIS','WE'=>'westlake','ZA'=>'ZETA','FR'=>'Fullrun','KO'=>'Kormoran','TG'=>'Triangle','B1'=>'BALKAR','T0'=>'TRAZANO','A2'=>'Autogreen','M4'=>'MINNAL','P0'=>'POWERTRAC','M2'=>'MATRIC','80'=>'RCBLDE','MG'=>'MIRAGE','GS'=>'GOALST','KN'=>'KENDA','RB'=>'ROYAL','M1'=>'MAZZINI','RO'=>'ROTALLA','DL'=>'DELINTE','G1'=>'GOVIND','UN'=>'UNIROYAL','W1'=>'WINRUN','RY'=>'RYDANZ','BL'=>'BLACKLION','IM'=>'IMPERIAL','FW'=>'FULLWAY','S6'=>'SUPERIA','T5'=>'TRISTAR','JI'=>'JINYU','MV'=>'MINERVA','FO'=>'FORTUNA','RS'=>'ROADSTONE','IO'=>'NITTO INVO','CR'=>'COOPER','C7'=>'CST','WF'=>'WINDFORCE','GW'=>'GOLDWAY','SC'=>'SECURITY','D1'=>'DURUN','MK'=>'MAXTREK','RU'=>'RUNWAY','DN'=>'DEESTONE','S0'=>'STARCO','37'=>'RST','GT'=>'GITI','BF'=>'BFGOODRICH','86'=>'MICKY THOMPSON','AT'=>'ATTURO','GF'=>'GOFORM','BB'=>'Boristar','ZY'=>'Z-TYRE','C3'=>'CENTARA','SL'=>'SAILUN','P6'=>'Protector','AO'=>'APOLLO','IL'=>'ILINK','DX'=>'DEXTERO','CE'=>'CEAT','AM'=>'ARMSTNG','IS'=>'INSTATE','AI'=>'AOTELI','FX'=>'FIREMX','K2'=>'KPATOS','VT'=>'VITORA','K1'=>'KNGRUN','ZX'=>'ZMAX','ZE'=>'ZEETEX','KT'=>'KNGSTAR','QE'=>'GAZELL','YT'=>'YATONE','DY'=>'DYNAMO','R8'=>'RDMRCH','RD'=>'RADAR','TX'=>'TRCMAX','FP'=>'FARRIDE','C2'=>'CHURCH','A8'=>'ATLS','PE'=>'PETLS','CQ'=>'COMSAL','D2'=>'DELI','DR'=>'DURO','JO'=>'JOURNE','H1'=>'HILO',
         ];

        
    
       $header = array_shift($fileContent); // Remove the first row as the header
        $header = array_map(function($key) {
            return str_replace(' ', '_', $key);
        }, $header);

        TyresProduct::where('tyre_supplier_name', $supplierName)
            ->where('supplier_id', $supplierId)
            ->where('instock', 0)
            ->delete();
    
        if (!TyresProduct::exists()) {
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }
    
        $insertData = [];
        $batchSize = 500;
 
        foreach ($fileContent as $line) {
            if (empty($line)) {
                continue;
            }
    
            $row = array_combine($header, $line);
           

            if (($row['STEAN']!='') && ($row['UNITCOST'] ?? 0) > 1 && ($row['QUANTITY'] ?? 0) > 1 && !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM']) && isset($row['TYNOISEDB']) && $row['TYNOISEDB'] > 0) {
                 
                $brandId = null;
                $brandCode = $row['MANUFCTR'] ?? null;
                $brandName = isset($cm[$brandCode]) ? $cm[$brandCode] : null;
    
                if ($brandName) {
                    $brand = tyre_brands::where('name', '=', $brandName)->first();
                    if ($brand) {
                        $brandId = $brand->brand_id;
                    } else {
                        try {
                            $brandId = tyre_brands::insertGetId([
                                'name' => $brandName,
                                'slug' => Str::slug($brandName),
                                'promoted' => 0,
                                'image' => Str::slug($brandName) . '.jpg',
                                'sort_order' => 1,
                                'status' => 1,
                                'product_type' => 'tyre',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to create tyre brand: ' . $brandName);
                            continue; 
                        }
                    }
                } else {
                    \Log::warning('Manufacturer code not found in mapping: ' . $brandCode);
                    continue; 
                }
    
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[$this->normalizeKey($key)] = $value;
                }
    
                $sku = $row['STCODE'] ?? $this->generateRandom13DigitNumber() . 'A';
                $ean = $row['STEAN'] ?? $this->generateRandom13DigitNumber() . 'Z';
    
                if (($row['QUANTITY'] ?? 0) > 1) {
                    $quantity = $row['QUANTITY'];
                    $status = 1;
                } else {
                    $quantity = 0;
                    $status = 0;
                }
    
                $tyre_runflat = isset($row['RUNFLAT']) && $row['RUNFLAT'] === 'T' ? 1 : 0;
                $tyre_extraload = isset($row['EXLOAD']) && $row['EXLOAD'] === 'T' ? 1 : 0;
               
                $seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $season = isset($row['TYSEASON']) && isset($seasonMap[strtoupper($row['TYSEASON'])])
                    ? $seasonMap[strtoupper($row['TYSEASON'])]
                    : 'Summer';

                $tyre_data = [
                    'tyre_sku' => $sku,
                    'tyre_ean' => $ean,
                    'tyre_quantity' => (int)$quantity,
                    'tyre_price' => $row['UNITCOST'],
                    'tyre_brand_id' => $brandId,
                    'tyre_season' => $season ?? null,
                    'tyre_width' => $row['SECTION'] ?? null,
                    'tyre_profile' => $row['PROFILE'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'tyre_loadindex' => $row['LOADIX'] ?? null,
                    'status' => $status,
                    'tyre_fullyfitted_price' => ($row['UNITCOST'] ?? 0) + 25,
                    'trade_costprice' => ($row['UNITCOST'] ?? 0) + 4.84,
                    'tyre_brand_name' => $brandName,
                    'tyre_model' => $row['TREADPAT'] ?? null,
                    'tyre_fuel' => $row['TYFUELC'] ?? null,
                    'tyre_wetgrip' => $row['TYWGC'] ?? null,
                    'tyre_noisedb' =>$row['TYNOISEDB'] ?? null,
                    'tyre_runflat' => $tyre_runflat,
                    'tyre_extraload' => $tyre_extraload,
                    'vehicle_type' => 'car',
                    'tyre_weight' => $row['WEIGHT'],
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'lead_time' => '',
                    'date_available' => now(),
                    'tyre_image' => '',
                    
                    'supplier_id' => $supplierId,
                    'tyre_supplier_name' => $supplierName,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'tyre_description' => trim(
                        $season . ' Tyre ' .
                        ($brandName ?? '') . ' ' .
                        ($row['TREADPAT'] ?? '') . ' ' .
                        ($row['SECTION'] ?? '') . '/' .
                        ($row['PROFILE'] ?? '') . 'R' .
                        ($row['RIM'] ?? '') . ' ' .
                        ($row['LOADIX'] ?? '') . ' ' .
                        ($row['SPEED'] ?? '') . ' ' .
                        ($tyre_runflat ? 'RFT' : '') . ' ' .
                        ($tyre_extraload ? 'XL' : '')
                    ),
                ];
                //dd($tyre_data);
                $insertData[] = $tyre_data;
            
                if (count($insertData) >= $batchSize) {
                    TyresProduct::insert($insertData);
                    $insertData = [];
                }
            }
        }

        if (!empty($insertData)) {
            \Log::info('Inserting ' . count($insertData) . ' products for supplier ' . $supplierName);
            TyresProduct::insert($insertData);
        }
    
        
        DB::table('suppliers')
        ->where('id', $supplierId)
        ->update(['updated_at' => now()]);
    }
    // private function importOwnStockCsv($fileContent, $supplierId, $supplierName)
    // {
    //     // Log::info('Error during importOwnStockCsv upload');
    // }
}
