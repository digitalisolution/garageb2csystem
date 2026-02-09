<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyrePricing extends Model
{
    use HasFactory;
    protected $primaryKey = 'pricing_id'; // Default primary key

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps = false;

    protected $fillable = [
        'pricing_name',
        'supplier_id',
        'order_type_id',
        'sort_order',
        'margin_type',
        'default_price',
        'default_price_type',
        'price_by_size',
        'product_type',
        'status'
    ];

    protected $casts = [
        'price_data' => 'array',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tyre_pricing';
}