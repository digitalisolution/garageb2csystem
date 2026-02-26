<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarService extends Model
{
    use HasFactory;
    protected $table = 'car_services';
    protected $primaryKey = 'service_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['name','garage_id', 'service_lead_time', 'slug', 'content','service_commission_price', 'tax_class_id', 'image', 'inner_image', 'display_status', 'status', 'meta_title', 'meta_description', 'meta_keywords', 'sort_order', 'price_type', 'cost_price', 'service_banner_path', 'service_features', 'service_whats_include', 'robots_noindex_follow', 'exclude_sitemap'];
   public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id');
    }
}
