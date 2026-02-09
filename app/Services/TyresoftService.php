<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Workshop;
use Gloudemans\Shoppingcart\Facades\Cart; // If using Shoppingcart package


class TyresoftService
{
    protected $saveDirectory;
    protected $supplierEmail;
    protected $externalRefAppend;
    protected $api;

    public function __construct(array $supplierDetails)
    {
        $this->saveDirectory = rtrim($supplierDetails['tyresoft_dir_path'], '/') . '/';
        $this->api = $supplierDetails;
        $this->supplierEmail = $supplierDetails['tyresoft_supplieremail'] ?? null;
        $this->externalRefAppend = $supplierDetails['external_ref_append'] ?? 'REF';
    }

    public function placeApiOrder(string $reference, array $products, string $jobid = null): array
    {

        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = $jobid
            ? "Order_{$this->externalRefAppend}_" . format_job_number($jobid) . "-{$timestamp}.csv"
            : "Order_{$this->externalRefAppend}_{$reference}-{$timestamp}.csv";
        $externalReference = $jobid
            ? "{$this->externalRefAppend}-job-" . format_job_number($jobid)
            : "{$this->externalRefAppend}-{$reference}";

        $list = $this->prepareCsvData($products, $externalReference, $jobid,$reference);

        if (!File::exists($this->saveDirectory)) {
            File::makeDirectory($this->saveDirectory, 0777, true);

        }

            $tempPath = storage_path("app/tmp_{$fileName}");
            $file = fopen($tempPath, 'w');
            foreach ($list as $line) {
                fputcsv($file, $line);
            }
            fclose($file);

        if ($this->api['tyresoft_upload_mode'] === 'ftp') {
        Log::info("Uploading to FTP: $fileName");
        $uploaded = $this->uploadToFtp($fileName, $tempPath);

        unlink($tempPath);

        if (!$uploaded) {
            return ['status' => 'danger', 'msg' => 'FTP upload failed.'];
        }
    } else {
        if (!File::exists($this->saveDirectory)) {
            File::makeDirectory($this->saveDirectory, 0777, true);
        }
        File::move($tempPath, $this->saveDirectory . $fileName);
    }

    return [
    'api_order_id' => 'tyresoft-' . rand(10000, 90000),
    'type' => $this->api['tyresoft_upload_mode'],
    'msg' => 'Order placed successfully',
    'status' => 'success',
    'details' => array_map(function ($item) use ($reference) {
        return [
            'sku' => $item['sku'] ?? null,
            'ean' => $item['ean'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'supplier' => $item['supplier'] ?? null,
            'reference' => 'JOB-' . $reference,
            'api_order_id' => rand(10000, 90000), // consistent with above
        ];
    }, $products),
    ];

    }

    private function prepareCsvData(array $products, $externalReference, $jobid = null,$reference)
    {
        $header = [
            "Channel Origin", "External Reference", "Vehicle VRM", "Sale Date", "Sale Notes", "Customer Name",
            "Customer Address 1", "Customer City", "Customer County", "Customer Postcode", "Customer Country", "Customer Email", "Customer Telephone", "Item Type", "Item Code", "Item Product EAN", "Product Description","Item Quantity",
            "Item Unit Cost", "Item Source Supplier", "Payment Method", "External Payment Reference", "Order Type"
        ];

        $rows = [$header];

        foreach ($products as $product) {
            $jobData = $jobid ? ($product['job_data'] ?? []) : [
                'item_type' => 'Product',
                'payment_method' => '',
                'datecreated' => now(),
            ];

            $sku = $product['sku'] ?? $product['description'] ?? '';
            $date = now()->format('Y-m-d_H-i-s');

            if ($reference) {
                $workshop = Workshop::find($reference);
                if (!$workshop) continue;
                if ($workshop) {
                    $rows[] = [
                        $workshop->workshop_origin,
                        $externalReference,
                        $workshop->vehicle_reg_number,
                        $date,
                        $workshop->notes,
                        $workshop->name,
                        $workshop->address,
                        $workshop->city,
                        $workshop->county,
                        $workshop->zone,
                        'UK',
                        $workshop->email,
                        $workshop->mobile,
                        'Product',
                        $sku,
                        $product['ean'] ?? '',
                        $product['description']?? '',
                        $product['quantity'] ?? 1,
                        $product['cost_price'] ?? '',
                        $product['supplier'] ?? '',
                        $workshop->payment_method,
                        '',
                        $workshop->fitting_type === 'mobile_fitted' ? 'mobile' : 'fully_fitted',
                    ];

                }
            }

        
        }
        Log::info('Tyresoft CSV rows count: ' . count($rows));

        return $rows;
    }

    private function uploadToFtp($remoteFileName, $localPath)
    {
        $ftpServer = $this->api['tyresoft_ftp_host'];
        $ftpUser = $this->api['tyresoft_ftp_username'];
        $ftpPass = $this->api['tyresoft_ftp_password'];
        $remoteDir = 'Orders';

        $connId = ftp_connect($ftpServer);
        if (!$connId) return false;

        $login = ftp_login($connId, $ftpUser, $ftpPass);
        if (!$login) return false;

        ftp_pasv($connId, true);
        @ftp_mkdir($connId, $remoteDir);
        @ftp_mkdir($connId, "{$remoteDir}");

        $uploadSuccess = ftp_put($connId, "{$remoteDir}/{$remoteFileName}", $localPath, FTP_BINARY);

        ftp_close($connId);
        return $uploadSuccess;
    }
}
