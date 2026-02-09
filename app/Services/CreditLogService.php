<?php

namespace App\Services;

use App\Models\VrmSmsCredit;
use App\Models\VrmCreditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditLogService
{
    public function useVrmCredit(
        string $credit_type,
        string $vrm,
        int $userId,
        string $reason = '',
        string $origin = 'api'
    ): array {
        return DB::transaction(function () use ($credit_type, $vrm, $userId, $reason, $origin) {
            $currentBalance = $this->getAvailableCredit($credit_type);

            if ($currentBalance <= 0) {
                throw new \RuntimeException("Insufficient {$credit_type} credits. Available: {$currentBalance}.");
            }

            $newBalance = $currentBalance - 1;

            $log = VrmCreditLog::create([
                'user_id' => $userId,
                'credit_type' => $credit_type,
                'vrm' => $vrm,
                'credit_used' => '1',
                'credit_available' => $newBalance,
                'reason' => $reason ?: "Used 1 {$credit_type} credit for {$vrm}",
                'origin_type' => $origin,
                'used_date' => now(),
            ]);

            return [
                'success' => true,
                'used' => 1,
                'previous_balance' => $currentBalance,
                'new_balance' => $newBalance,
                'log_id' => $log->id,
                'status' => $newBalance > 10 ? 'ok' : ($newBalance > 0 ? 'low' : 'expired'),
            ];
        });
    }

    public function addVrmCredit(
        string $credit_type,
        int $userId,
        int $amount,
        float $pricePerUnit = 0.0,
        string $reason = '',
        string $origin = 'admin'
    ): array {
        
        return DB::transaction(function () use ($credit_type, $userId, $amount, $pricePerUnit, $reason, $origin) {
            $subtotal = $amount * $pricePerUnit;
            $vat = $subtotal * 0.20;
            $total = $subtotal + $vat;

            $purchase = VrmSmsCredit::create([
                'type' => $credit_type,
                'quantity' => $amount,
                'price' => $pricePerUnit,
                'subtotal' => $subtotal,
                'total_with_vat' => $total,
            ]);
            $newBalance = $this->getTotalPurchased($credit_type) - $this->getTotalUsed($credit_type);

            $log = VrmCreditLog::create([
                'user_id' => $userId,
                'credit_type' => $credit_type,
                'vrm' => null,
                'credit_used' => '0',
                'credit_available' => $newBalance,
                'reason' => $reason ?: "Added {$amount} {$credit_type} credits (Purchase #{$purchase->id})",
                'origin_type' => $origin,
                'used_date' => now(),
            ]);
           
            return [
                'success' => true,
                'added' => $amount,
                'new_balance' => $newBalance,
                'purchase_id' => $purchase->id,
                'log_id' => $log->id,
            ];
        });
    }

    public function getAvailableCredit(string $credit_type): int
    {
        return $this->getTotalPurchased($credit_type) - $this->getTotalUsed($credit_type);
    }

    private function getTotalPurchased(string $credit_type): int
    {
        return VrmSmsCredit::where('type', $credit_type)->sum('quantity');
    }
    private function getTotalUsed(string $credit_type): int
    {
        return VrmCreditLog::where('credit_type', $credit_type)
            ->where('credit_used', '1') 
            ->count();
    }
}