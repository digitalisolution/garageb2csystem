<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EdenOrderService
{
    protected $saveDirectory;
    protected $supplierEmail;
    protected $externalRefAppend;
    protected $api;

    public function __construct(array $supplierDetails)
    {
        
        $this->saveDirectory = rtrim($supplierDetails['eden_dir_path'], '/') . '/';
        $this->api = $supplierDetails;
        $this->supplierEmail = $supplierDetails['supplier_email'];
        $this->externalRefAppend = $supplierDetails['external_ref_append'];
    }

    public function placeApiOrder($reference, $products, $jobId = '')
    {
        $fileName = 'Order_' . $this->externalRefAppend . '_' . ($reference ? 'workshop_id' . $reference : $reference) . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

        Log::channel('daily')->info("Eden API Order: {$fileName}");

        $header = [
            "Channel Name", "External Reference", "Vehicle VRM", "Sale Date", "Sale Notes", "Customer Name",
            "Customer Address 1", "Customer Address 2", "Customer Address 3", "Customer City", "Customer County",
            "Customer Postcode", "Customer Country", "Customer Email", "Customer Telephone", "Customer Mobile",
            "Item Type", "Item Code", "Item Product EAN", "Item Product Manufacturer Code", "Item Quantity",
            "Item Unit Cost", "Item Source Supplier", "Payment Method", "External Payment Reference",
            "Customer Account Number", "Flag"
        ];

        $rows = [$header];

        foreach ($products as $product) {
            $jobData = $product['job_data'] ?? [];

            $rows[] = [
                'Website',
                $this->externalRefAppend . '-' . ($jobId ?: $reference),
                $jobData['vrm'] ?? '',
                Carbon::parse($jobData['datecreated'] ?? now())->format('Ymd'),
                $jobData['clientnote'] ?? '',
                $jobData['c_firstname'] ?? '',
                $jobData['billing_street'] ?? '',
                '',
                '',
                $jobData['billing_city'] ?? '',
                $jobData['billing_state'] ?? '',
                $jobData['billing_zip'] ?? '',
                'United Kingdom',
                $jobData['clientemail'] ?? '',
                $jobData['c_phonenumber'] ?? '',
                $jobData['c_phonenumber'] ?? '',
                $jobData['item_type'] ?? 'Product',
                $product['sku'] ?? '',
                $product['ean'] ?? '',
                $jobData['mpn'] ?? '',
                $product['quantity'] ?? 1,
                $product['price'] ?? 0,
                $jobData['ref_id'] ?? '',
                'Credit Card',
                '',
                '',
                ''
            ];
        }

        $path = 'eden_orders/' . $fileName;

        // Save CSV
        $csvContent = collect($rows)->map(function ($row) {
            return implode(',', array_map(fn($value) => '"' . str_replace('"', '""', $value) . '"', $row));
        })->implode("\n");

        Storage::disk('local')->put($path, $csvContent);

        // Upload via FTP
        $localFile = storage_path('app/' . $path);
        $success = $this->ftpUpload($localFile, $fileName);

        return $success
            ? ['status' => 'success', 'message' => 'Order placed', 'file' => $fileName]
            : ['status' => 'error', 'message' => 'FTP upload failed'];
    }

    protected function ftpUpload($localFile, $remoteFile)
    {
        $ftp = ftp_connect($this->api['eden_ftp_host']);
        if (!$ftp) return false;

        $login = ftp_login($ftp, $this->api['eden_ftp_username'], $this->api['eden_ftp_password']);
        if (!$login) return false;

        ftp_pasv($ftp, true);

        $remotePath = 'Orders/' . $remoteFile;

        if (!@ftp_put($ftp, $remotePath, $localFile, FTP_ASCII)) {
            ftp_close($ftp);
            return false;
        }

        ftp_close($ftp);
        return true;
    }
}
