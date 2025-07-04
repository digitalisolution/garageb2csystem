<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class TyresProduct extends Model
{

    // use SoftDeletes;
    protected $connection = 'mysql_vehicle_details';
    // Disable automatic timestamps
    public $timestamps = true;

    protected $primaryKey = 'product_id';
    // Fillable fields for mass assignment

  public function getConnectionName()
{
    // $sharedDomains = ['www.digitalideasltd.in', 'b2b.garage-automation.com'];
    $sharedDomains = [];

    // If CLI (e.g., schedule, artisan command)
    if (app()->runningInConsole()) {
        // Use fake host override if provided
        $cliHost = $_SERVER['APP_FAKE_HOST'] ?? null;

        if ($cliHost && in_array($cliHost, $sharedDomains)) {
            return 'mysql_vehicle_details';
        }

        return config('database.default'); // fallback
    }

    // Web-based
    $host = request()->getHost();
    if (in_array($host, $sharedDomains)) {
        return 'mysql_vehicle_details';
    }

    return config('database.default'); // default fallback
}

 public function getTable()
    {
        if (app()->runningInConsole()) {
            $cliHost = $_SERVER['APP_FAKE_HOST'] ?? null;

            return match ($cliHost) {
                // 'b2b.garage-automation.com'     => 'tyres_product_gloucester',
                // 'www.digitalideasltd.in'        => 'tyres_product_gloucester',
                default                         => 'tyres_product',
            };
        }

        $host = request()->getHost();

        return match ($host) {
            // 'b2b.garage-automation.com'     => 'tyres_product_gloucester',
            // 'www.digitalideasltd.in'        => 'tyres_product_gloucester',
            default                         => 'tyres_product',
        };
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
