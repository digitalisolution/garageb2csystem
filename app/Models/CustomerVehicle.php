<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVehicle extends Model
{
    use HasFactory;

    // Specify the table name if it's not the default (customer_vehicles)
    protected $table = 'customer_vehicle';

    // Disable timestamps if the table does not have `created_at` and `updated_at` columns
    public $timestamps = true; // Set to false if your table doesn't have these columns

    // Define the fillable fields (columns that can be mass-assigned)
    protected $fillable = [
        'customer_id',
        'vehicle_detail_id',
        'created_at',
        'updated_at'
    ];

    // Define relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vehicleDetail()
    {
        return $this->belongsTo(VehicleDetail::class, 'vehicle_detail_id');
    }
}