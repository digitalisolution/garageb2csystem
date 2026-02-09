<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VrmSmsCredit extends Model
{
    use HasFactory;
    protected $table = 'vrm_sms_credits';

    protected $fillable = [
        'type',
        'quantity',
        'price','subtotal','total_with_vat'
    ];
}

