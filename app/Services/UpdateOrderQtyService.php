<?php

namespace App\Services;

use App\Models\TyresProduct;
use App\Models\WorkshopTyre;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateOrderQtyService
{
    /**
     * 
     *
     * @param int $workshopId
     * @throws \Exception
     */
    public function updateStockQty($workshopId)
    {
        $tyreItems = WorkshopTyre::where('workshop_id', $workshopId)
            ->where('product_type', 'tyre')
            ->get();

        if ($tyreItems->isEmpty()) {
            Log::info('No tyre items found for workshop ID:', ['workshop_id' => $workshopId]);
            return;
        }

        foreach ($tyreItems as $item) {
            DB::beginTransaction();

            try {
                $tyre = TyresProduct::where('product_id', $item->product_id)
                    ->Where(function ($query) use ($item) {
                        $query->where('tyre_ean', $item->product_ean)
                              ->Where('tyre_sku', $item->product_sku);
                    })
                    ->lockForUpdate()
                    ->first();

                if ($tyre) {
                    $tyre->tyre_quantity -= $item->quantity;

                    if ($tyre->tyre_quantity < 0) {
                        throw new Exception("Insufficient stock for tyre: {$tyre->tyre_model}. Available: {$tyre->tyre_quantity}");
                    }

                    $tyre->save();
                } else {
                    Log::error('Tyre not found for stock update:', [
                        'product_id' => $item->product_id,
                        'product_ean' => $item->product_ean,
                        'product_sku' => $item->product_sku,
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating stock quantity:', [
                    'workshop_id' => $workshopId,
                    'product_id' => $item->product_id,
                    'product_ean' => $item->product_ean,
                    'product_sku' => $item->product_sku,
                    'error_message' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }
}