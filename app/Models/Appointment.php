<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment_form';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'vehicle_type',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'tyresize',
        'message',
        'choose_date',
        'choose_time',
    ];
}
