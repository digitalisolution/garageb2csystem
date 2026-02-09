<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPlugins extends Model
{
    protected $connection = 'mysql_vehicle_details';
    protected $table = 'client_plugins'; // your DB table


    protected $fillable = [
        'client_id',
        'secret_token',
        'website_name',
    ];
}
