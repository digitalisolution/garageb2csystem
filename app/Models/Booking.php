<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    // use HasFactory;
    protected $dates = ['start', 'end'];
    protected $fillable = ['workshop_id', 'title', 'start', 'end'];
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'workshop_id');
    }
    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

}
