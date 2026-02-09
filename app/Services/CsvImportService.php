<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CsvImportService
{
    // Function to import CSV data
    public function importCsvData($fileContent)
    {
        // Set a longer execution time to handle large data imports
        set_time_limit(300);

        // Ensure $fileContent is an array of rows (no need to implode if it's already an array)
        if (is_array($fileContent)) {
            $rows = $fileContent;
        } else {
            \Log::error('File content is not an array, unable to process.');
            return; // Exit if file content is not in the expected format
        }

        // Extract the header (first row)
        $header = array_shift($rows);

        // Delete existing data for the `bond` source
        DB::table('tyres_product')->where('tyre_source', 'bond')->delete();
        DB::statement("ALTER TABLE tyres_product AUTO_INCREMENT = 1");

        // Batch processing variables
        $insertData = [];
        $batchSize = 500;

        // Helper function to normalize keys
        function normalizeKey($key)
        {
            return strtolower(str_replace([' ', '_'], '', $key));
        }

        // Process each row in the CSV file
        foreach ($rows as $line) {
            if (empty($line)) {
                continue;
            }

            // Combine header and line into an associative array
            $row = array_combine($header, $line);

            // Check if row passes validation conditions
            if (
                ($row['PRICE'] ?? 0) > 1 || ($row['STOCKBAL'] ?? 0) > 0 &&
                !empty($row['SECTION']) && !empty($row['PROFILE']) && !empty($row['RIM']) && !empty($row['SPEED']) && !empty($row['LOAD_INDEX'])
            ) {
                // Handle manufacturer_id logic
                $manufacturerId = null;
                $brandName = $row['BRAND'] ?? null;

                if ($brandName) {
                    $brand = DB::table('tyre_brands')->where('name', '=', $brandName)->first();

                    if ($brand) {
                        $manufacturerId = $brand->manufacturer_id;
                    } else {
                        // Create a new brand entry
                        $newBrandId = DB::table('tyre_brands')->insertGetId([
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

                        $manufacturerId = $newBrandId;
                    }
                }

                // Normalize row keys
                $normalized_row = [];
                foreach ($row as $key => $value) {
                    $normalized_row[normalizeKey($key)] = $value;
                }

                // Prepare the tyre data for insertion
                $tyre_data = [
                    'sku' => $row['BOND_CODE'] ?? null,
                    'ean' => $row['EAN'] ?? null,
                    'quantity' => ($row['STOCKBAL'] ?? 0) >= 1 ? $row['STOCKBAL'] : 0,
                    'price' => $row['PRICE'] ?? null,
                    'manufacturer_id' => $manufacturerId,
                    'tyre_type' => $row['SEASON'] ?? null,
                    'tyre_width' => $row['SECTION'] ?? null,
                    'tyre_profile' => $row['PROFILE'] ?? null,
                    'tyre_diameter' => $row['RIM'] ?? null,
                    'tyre_speed' => $row['SPEED'] ?? null,
                    'status' => ($row['STOCKBAL'] ?? 0) >= 1 ? 1 : 0,
                    'price_fullyfitted' => ($row['PRICE'] ?? 0) + 20,
                    'tyre_brand_name' => $brandName,
                    'model' => $row['PATTERN'] ?? null,
                    'description' => $row['PATTERN'] . ' ' . ($row['SECTION'] ?? '') . ' ' . ($row['PROFILE'] ?? '') . ' ' . ($row['RIM'] ?? ''),
                    'name' => $row['PATTERN'] . ' ' . ($row['SECTION'] ?? '') . ' ' . ($row['PROFILE'] ?? '') . ' ' . ($row['RIM'] ?? ''),
                    'tyre_source' => 'bond',
                    'date_added' => now(),
                    'date_modified' => now(),
                    'tyre_eco' => $row['FUEL'] ?? null,
                    'tyre_disfr' => $row['WET'] ?? '',
                    'price_type' => $row['price_type'] ?? 0,
                    'tyre_antiflat' => ($row['RFT'] === 'Yes' || $row['RFT'] === 'RFT') ? 1 : 0,
                    'tyre_reinforced' => ($row['XL'] === 'XL') ? 1 : 0,
                    'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? ''))),
                    'weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                    'product_type' => 'tyre',
                    'tax_class_id' => 9,
                    'date_available' => now(),
                    'image' => $row['IMAGE'] ?? null,
                    'offerprice' => $row['offerprice'] ?? 0,
                    'supplier_id' => $row['supplier_id'] ?? 2,
                ];

                // Add data to the insert batch
                $insertData[] = $tyre_data;

                // Insert data in batches for efficiency
                if (count($insertData) >= 500) {
                    DB::table('tyres_product')->insert($insertData);
                    $insertData = [];
                }
            }
        }

        // Insert any remaining data that wasn't inserted in the batch
        if (!empty($insertData)) {
            DB::table('tyres_product')->insert($insertData);
        }
    }
}
