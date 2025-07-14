<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarService extends Model
{
    use HasFactory;
    protected $table = 'car_services';
    protected $primaryKey = 'service_id'; // Specify the primary key column
    public $incrementing = true; // If the primary key is auto-incrementing
    protected $keyType = 'int';
    protected $fillable = ['name', 'service_lead_time', 'slug', 'content', 'tax_class_id', 'image', 'inner_image', 'display_status', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'sort_order', 'price_type', 'cost_price', 'service_banner_path', 'robots_noindex_follow', 'exclude_sitemap'];

    // Relationship for subpages


}
