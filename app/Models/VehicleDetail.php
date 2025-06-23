<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class VehicleDetail extends Model
{
    use SoftDeletes;

    protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

    // Cast date fields
    protected $casts = [
        'vehicle_mot_expiry_date' => 'date:Y-m-d',
    ];

    // Optional: Only if you need custom logic
    public function setVehicleMotExpiryDateAttribute($value)
    {
        $this->attributes['vehicle_mot_expiry_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function getVehicleMotExpiryDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_vehicle')->withTimestamps();
    }
}