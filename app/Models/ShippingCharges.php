<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharges extends Model
{
    use HasFactory;

    // Define the primary key
    protected $primaryKey = 'setting_id';

    // Define the table name
    protected $table = 'shipping_charges';

    // Define the fillable fields
    protected $fillable = [
        'code',         // e.g., 'shippingbyproduct', 'shippingbypostcode'
        'key',          // e.g., 'shippingbyproduct', 'shippingbyproduct_status'
        'value',        // Serialized data (e.g., milesData, postcodeData)
    ];

    // Optionally, if you have timestamps in your table
    public $timestamps = false; // Set to false if your table doesn't use timestamps
}