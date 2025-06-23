<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class TyresProduct extends Model
{

    // use SoftDeletes;
    protected $connection = 'mysql_vehicle_details';
    protected $table = 'tyres_product';
    // Disable automatic timestamps
    public $timestamps = true;

    protected $primaryKey = 'product_id';
    // Fillable fields for mass assignment

    public function getConnectionName()
    {
        // Domains that use the shared tyre inventory
        $sharedDomains = ['www.digitalideasltd.in', 'b2b.garage-automation.com'];

        $host = request()->getHost();

        if (in_array($host, $sharedDomains)) {
            return 'mysql_vehicle_details';
        }
    }


    protected $fillable = [
        'product_id',
        'instock',
        'tyre_description',
        'tyre_model',
        'tyre_sku',
        'tyre_ean',
        'tyre_quantity',
        'tyre_image',
        'tyre_brand_id',
        'tyre_brand_name',
        'product_type',
        'tyre_price',
        'tyre_margin',
        'tax_class_id',
        'date_available',
        'tyre_weight',
        'sort_order',
        'status',
        'created_at',
        'updated_at',
        'tyre_season',
        'tyre_width',
        'tyre_profile',
        'tyre_diameter',
        'tyre_speed',
        'tyre_noisedb',
        'tyre_fuel',
        'tyre_wetgrip',
        'tyre_extraload',
        'tyre_runflat',
        'tyre_loadindex',
        'vehicle_type',
        'car_manufacturer',
        'tyre_supplier_name',
        'supplier_id',
        'tyre_collection_price',
        'tyre_fullyfitted_price',
        'tyre_mailorder_price',
        'tyre_mobilefitted_price',
        'lead_time',
        'trade_costprice'
    ];
    public function brand()
    {
        // Replace 'manufacturer_id' and 'id' with the correct column names
        return $this->belongsTo(tyre_brands::class, 'tyre_brand_id', 'brand_id');
    }
}
