<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUs extends Model
{


    protected $table = 'about_us'; // Make sure this matches your table name

    // Specify the attributes you want to allow mass assignment for
    protected $fillable = ['title', 'description']; // Replace with actual columns in your services table

}

