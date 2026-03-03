<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;

class GaragePayoutInvoice extends Model
{
    protected $table = 'garage_payout_invoices';
    
    protected $guarded = [];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relations
    public function garagePayout(): BelongsTo
    {
        return $this->belongsTo(GaragePayout::class);
    }

    public function garage(): BelongsTo
    {
        return $this->hasOneThrough(
            Garage::class,
            GaragePayout::class,
            'id',
            'id',
            'garage_payout_id',
            'garage_id'
        );
    }

    // Accessors
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path 
            ? asset('storage/' . ltrim($this->pdf_path, '/')) 
            : null;
    }

    public function isSent(): bool
    {
        return $this->status === 'sent' && $this->sent_at !== null;
    }

    public function isVoid(): bool
    {
        return $this->status === 'void';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'issued')->whereNull('sent_at');
    }

    public function scopeForGarage($query, $garageId)
    {
        return $query->whereHas('garagePayout', fn($q) => $q->where('garage_id', $garageId));
    }


}