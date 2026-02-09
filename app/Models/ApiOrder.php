<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiOrder extends Model
{
    protected $table = 'api_tyre_orders';
    public $timestamps = false;
    protected $fillable = [
        'workshop_id',
        'api_order_id',
        'date_added',
        'status',
        'order_type',
        'supplier',
        'reference',
        'ean',
        'sku',
        'quantity'
    ];
}