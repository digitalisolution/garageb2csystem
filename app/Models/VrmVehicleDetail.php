<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VrmVehicleDetail extends Model
{
    use HasFactory;
    protected $connection = 'mysql_vehicle_details'; // Common DB connection
    protected $table = 'vrm_vehicle_details'; // Table for this model
}
