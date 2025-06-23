<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarageDetails extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $table = 'garage_details';
    // Fillable fields for mass assignment
    protected $fillable = [
        'garage_name',
        'company_number',
        'vat_number',
        'eori_number',
        'phone',
        'mobile',
        'email',
        'street',
        'city',
        'zone',
        'country',
        'description',
        'garage_opening_time',
        'logo',
        'banner',
        'favicon',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_youtube',
        'google_map_link',
        'longitude',
        'latitude',
        'google_reviews_link',
        'google_reviews_stars',
        'google_reviews_count',
        'website_url',
        'notes',
        'status'
    ];
}
