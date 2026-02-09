<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionCounty extends Model
{
    use HasFactory;

    protected $table = 'region_county'; // Specify the table name
    protected $primaryKey = 'zone_id'; // Specify the primary key
    public $timestamps = false; // Disable timestamps if not used

    protected $fillable = [
        'country_id',
        'name',
        'code',
        'status',
    ];
}