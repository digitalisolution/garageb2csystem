<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GaragePayout extends Model
{
    protected $fillable = [
        'garage_id',
        'workshop_id',
        'customer_paid_amount',
        'platform_commission',
        'payout_amount',
        'card_processing_fee',
        'revolut_transaction_id',
        'status',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'customer_paid_amount' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'payout_amount' => 'decimal:2',
    ];

    public function garage()
    {
        return $this->belongsTo(Garage::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsPaid(string $transactionId)
    {
        $this->update([
            'status' => 'completed',
            'revolut_transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }
}