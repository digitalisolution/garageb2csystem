<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\ApiOrder;
use App\Models\WorkshopTyre;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiOrderingService
{
    protected $supplierServiceFactory;

    public function __construct(SupplierServiceFactory $supplierServiceFactory)
    {
        $this->supplierServiceFactory = $supplierServiceFactory;
    }

    /*public function processApiOrder($orderId)
    {
        // Fetch the workshop associated with the order
        $workshop = Workshop::find($orderId);

        if (!$workshop) {
            Log::error('Workshop not found for API order processing.', ['order_id' => $orderId]);
            return;
        }

        // Fetch cart items (tyres and services) associated with the workshop
        $cartItems = WorkshopTyre::where('workshop_id', $orderId)->get();

        foreach ($cartItems as $item) {
            // Skip services for API ordering
            if ($item->product_type === 'service') {
                continue;
            }

            // Ensure the item is a tyre
            if ($item->product_type === 'tyre') {
                // Retrieve the supplier for the tyre from WorkshopTyre
                $supplier = $item->supplier;

                if (!$supplier) {
                    Log::error('Supplier missing for item:', ['item' => $item->toArray()]);
                    continue;
                }

                // Fetch supplier details from the suppliers table
                $supplierDetails = DB::table('suppliers')
                    ->where('supplier_name', $supplier)
                    ->first();

                // Check if API ordering is enabled for the supplier
                if ($supplierDetails && $supplierDetails->api_order_enable == 1) {
                    $supplierService = $this->supplierServiceFactory->getServiceForSupplier($supplier);
                    if (!$supplierService) {
                        Log::error("Service not found for supplier: $supplier");
                        continue;
                    }

                    try {
                        // Prepare the product details to send to the supplier service
                        $productDetails = [
                            'sku' => $item->product_sku,
                            'ean' => $item->product_ean,
                            'quantity' => $item->quantity,
                            'description' => $item->description,
                            'model' => $item->model,
                            'price' => $item->price,
                            'supplier' => $supplier,
                        ];

                        // Place the API order
                        $apiResponse = $supplierService->placeApiOrder($orderId, [$productDetails]);

                        if ($apiResponse['status'] === 'danger') {
                            Log::error('API Order failed:', ['response' => $apiResponse]);
                            continue;
                        }

                        // Save the API order response
                        $this->saveApiOrderResponse($orderId, $apiResponse);
                    } catch (\Exception $e) {
                        Log::error('Error placing API order:', [
                            'supplier' => $supplier,
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                } else {
                    Log::info('API ordering is not enabled for supplier:', ['supplier' => $supplier]);
                }
            }
        }
    }*/

    public function processApiOrder($orderId)
        {
            $workshop = Workshop::find($orderId);
            if (!$workshop) {
                Log::error('Workshop not found for API order processing.', ['order_id' => $orderId]);
                return;
            }

            // Fetch all tyres (excluding services)
            $tyreItems = WorkshopTyre::where('workshop_id', $orderId)
                ->where('product_type', 'tyre')
                ->get();

            // Group tyres by supplier
            $itemsBySupplier = $tyreItems->groupBy('supplier');

            foreach ($itemsBySupplier as $supplier => $items) {
                if (!$supplier) {
                    Log::error('Supplier missing for items.');
                    continue;
                }

                $supplierDetails = DB::table('suppliers')
                    ->where('supplier_name', $supplier)
                    ->first();

                if (!$supplierDetails || $supplierDetails->api_order_enable != 1) {
                    Log::info('API ordering not enabled for supplier:', ['supplier' => $supplier]);
                    continue;
                }

                $supplierService = $this->supplierServiceFactory->getServiceForSupplier($supplier);
                if (!$supplierService) {
                    Log::error("Service not found for supplier: $supplier");
                    continue;
                }

                // Prepare product list for this supplier
                $productList = [];
                foreach ($items as $item) {
                    $productList[] = [
                        'sku' => $item->product_sku,
                        'ean' => $item->product_ean,
                        'quantity' => $item->quantity,
                        'description' => $item->description,
                        'model' => $item->model,
                        'price' => $item->price,
                        'supplier' => $supplier,
                    ];
                }

                try {
                    $apiResponse = $supplierService->placeApiOrder($orderId, $productList);

                    if ($apiResponse['status'] === 'danger') {
                        Log::error('API Order failed:', ['response' => $apiResponse]);
                        continue;
                    }

                    $this->saveApiOrderResponse($orderId, $apiResponse);

                } catch (\Exception $e) {
                    Log::error('Error placing API order:', [
                        'supplier' => $supplier,
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        }


   protected function saveApiOrderResponse($orderId, $apiResponse)
{
    try {
        $details = $apiResponse['details'] ?? [];
        foreach ($details as $detail) {
            ApiOrder::create([
                'workshop_id' => $orderId,
                'api_order_id' => $detail['api_order_id'] ?? null,
                'order_type' => $apiResponse['type'] ?? 'api',
                'supplier' => $detail['supplier'] ?? null,
                'status' => $apiResponse['status'] === 'success' ? 1 : 0,
                'reference' => $detail['reference'] ?? 'job-',
                'ean' => $detail['ean'] ?? '123ean',
                'sku' => $detail['sku'] ?? '123sku',
                'quantity' => $detail['quantity'] ?? 0,
                'date_added' => Carbon::now(),
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Error saving API order response:', [
            'workshop_id' => $orderId,
            'error_message' => $e->getMessage(),
        ]);
    }
}

}