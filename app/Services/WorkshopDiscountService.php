<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\WorkshopTyre;
use App\Models\WorkshopWheel;
use App\Models\WorkshopService;
use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class WorkshopDiscountService
{
    /**
     * Apply or update discount on a workshop (and invoice if exists)
     */
    public function applyAutoDiscount($workshopId)
    {
        $workshop = Workshop::find($workshopId);

        if (!$workshop) {
            Log::warning("Workshop not found for discount.", ['workshop_id' => $workshopId]);
            return;
        }

        // ✅ customer optional
        $customer = $workshop->customer_id
            ? Customer::find($workshop->customer_id)
            : null;

        if (!$customer) {
            Log::info("No customer — skipping discount.", ['workshop_id' => $workshopId]);
            return; // 👈 IMPORTANT: silently skip
        }

        // ✅ group optional
        $group = $customer->customer_group_id
            ? CustomerGroup::find($customer->customer_group_id)
            : null;

        if (!$group) {
            Log::info("No customer group — skipping discount.", ['workshop_id' => $workshopId]);
            return;
        }

        // ===============================
        // DISCOUNT LOGIC
        // ===============================

        if (!$group->discount_type || !$group->discount_value) {
            return;
        }

        $discountBase = 0;
        $tyreTotal = 0;
        $wheelTotal = 0; 
        $serviceTotal = 0;

        if (in_array('service', (array)$group->product_type)) {
            $serviceTotal = WorkshopService::where('workshop_id', $workshopId)->sum('service_price');
            $discountBase += $serviceTotal;
        }

        if (in_array('tyre', (array)$group->product_type)) {
            $tyreTotal = WorkshopTyre::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->sum('price');

            $discountBase += $tyreTotal;
        }
        if (in_array('wheel', (array)$group->product_type)) {
            $wheelTotal = WorkshopWheel::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->sum('price');

            $discountBase += $wheelTotal;
        }

        if ($discountBase <= 0) {
            return;
        }

        $discountAmount = $group->discount_type === 'percentage'
            ? ($discountBase * $group->discount_value / 100)
            : $group->discount_value;

        $discountAmount = round($discountAmount, 2);

        // rollback previous discount
        if ($workshop->discount_price > 0) {
            $workshop->balance_price += $workshop->discount_price;
        }

        $newBalance = max(0, $workshop->balance_price - $discountAmount);

        $workshop->discount_type = $group->discount_type;
        $workshop->discount_value = $group->discount_value;
        $workshop->discount_price = $discountAmount;
        $workshop->balance_price = $newBalance;
        $workshop->save();

        // ===============================
        // INVOICE UPDATE
        // ===============================

        $invoice = Invoice::where('workshop_id', $workshopId)->first();

        if ($invoice) {
            $invoice->discount_type = $group->discount_type;
            $invoice->discount_value = $group->discount_value;
            $invoice->discount_price = $discountAmount;
            $invoice->balance_price = $newBalance;
            $invoice->save();
        }

        // ===============================
        // DUE DATE LOGIC
        // ===============================

        if (!$group->due_date_option) {
            return;
        }

        $dueOutDate = null;

        switch ($group->due_date_option) {
            case 'first_date':
                $dueOutDate = Carbon::now()->addMonthNoOverflow()->startOfMonth();
                break;

            case 'last_date':
                $dueOutDate = Carbon::now()->addMonthNoOverflow()->endOfMonth();
                break;

            case '30_days_invoice_date':
                if ($invoice && $invoice->invoice_date) {
                    $dueOutDate = Carbon::parse($invoice->invoice_date)->addDays(30);
                }
                break;

            case 'manual':
                if ($group->manual_due_date) {
                    $dueOutDate = Carbon::parse($group->manual_due_date)->addMonthNoOverflow();
                }
                break;
        }

        if ($dueOutDate) {
            $workshop->due_out = $dueOutDate;
            $workshop->save();

            if ($invoice) {
                $invoice->due_out = $dueOutDate;
                $invoice->save();
            }
        }
    }


}
