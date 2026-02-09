<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VrmCreditLog extends Model
{
    use HasFactory;

    protected $table = 'vrm_credit_logs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'credit_id',          
        'credit_type',       
        'vrm',            
        'credit_used',    
        'credit_available',   
        'reason',
        'used_date',       
        'user_id',           
        'origin_type',
    ];

    protected $casts = [
        'used_date' => 'datetime',
        'credit_available' => 'integer',
        'user_id' => 'integer',
        'credit_id' => 'integer',
    ];

    protected $dates = [
        'used_date',
    ];

    protected $attributes = [
        'used_date' => null,
        'credit_used' => '0',
        'credit_available' => 0,
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}