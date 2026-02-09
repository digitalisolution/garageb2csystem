<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $table = 'payment_records'; // Update with your actual table name
    public $timestamps = false;

    protected $fillable = [
        'workshop_id',
        'amount',
        'paymentmode',
        'paymentmethod',
        'date',
        'daterecorded',
        'note',
        'transactionid',
    ];
}
