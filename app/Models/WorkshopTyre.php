<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopTyre extends Model
{
    protected $fillable = [
        'workshop_id',
        'ref_type',
        'product_id',
        'description',
        'model',
        'supplier',
        'product_type',
        'margin_rate',
        'cost_price',
        'product_ean',
        'fitting_type',
        'shipping_postcode',
        'shipping_price',
        'shipping_tax_id',
        'tax_class_id',
        'product_sku',
        'quantity',
        'price',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
