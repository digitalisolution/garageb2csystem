<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    protected $table = 'countries'; // Specify the table name
    protected $primaryKey = 'country_id'; // Specify the primary key
    public $timestamps = false; // Disable timestamps if not used

    protected $fillable = [
        'country_id',
        'name',
        'iso_code',
        //'iso2_code',
        'status',
    ];
}