<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VrmApiResponse extends Model
{
    use HasFactory;
    protected $connection = 'mysql_vehicle_details'; // Common DB connection
    protected $table = 'vrm_api_response'; // Table for this model
    public $timestamps = false;
    protected $fillable = ['vrm', 'api_response', 'data_package', 'added_date'];
}
