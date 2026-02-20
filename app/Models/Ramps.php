<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ramps extends Model
{
    use HasFactory;
    protected $table = 'ramps';
    protected $primaryKey = 'ramp_id';
    protected $fillable = ['ramp_name', 'ramp_type', 'ramp_services', 'status'];

}
