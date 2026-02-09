<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharges extends Model
{
    use HasFactory;
    protected $primaryKey = 'setting_id';
    protected $table = 'shipping_charges';
    protected $fillable = [
        'code',
        'key',
        'value',
    ];

    public $timestamps = false;
}