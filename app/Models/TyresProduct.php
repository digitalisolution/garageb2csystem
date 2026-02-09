<?php

namespace App\Models;
use App\Models\Garage;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;

class TyresProduct extends Model
{
    protected $connection = 'mysql_vehicle_details';
    public $timestamps = true;

    protected $primaryKey = 'product_id';

    protected array $gloucesterDomains = [
        'www.gloucester-tyres.co.uk',
        'www.gloucestertyresltd.co.uk',
    ];

    protected array $tyrelabDomains = [
        'b2b.digitalideasltd.co.uk',
        'www.garage-automation.co.uk',
    ];

    protected function resolveHost(): ?string
    {
        $host = app()->runningInConsole()
            ? ($_SERVER['APP_FAKE_HOST'] ?? null)
            : request()->getHost();

        return $host ? strtolower($host) : null;
    }

  public function getConnectionName()
    {
        $host = $this->resolveHost();

        if (in_array($host, $this->gloucesterDomains) || in_array($host, $this->tyrelabDomains)) 
        {
            return 'mysql_vehicle_details';
        }

        return config('database.default');
    }

    public function getTable()
    {
        $host = $this->resolveHost();
        
        return match (true) {
            in_array($host, $this->gloucesterDomains) => 'tyres_product_gloucester',
            in_array($host, $this->tyrelabDomains)    => 'tyres_product_tyrelab',
            default                                   => 'tyres_product',
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
        'garage_id',
        'tyre_collection_price',
        'tyre_delivery_price',
        'tyre_fullyfitted_price',
        'tyre_mailorder_price',
        'tyre_mobilefitted_price',
        'lead_time',
        'trade_costprice'
    ];
    
    public function brand()
    {
        return $this->belongsTo(tyre_brands::class, 'tyre_brand_id', 'brand_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function garage()
    {
        return $this->belongsTo(Garage::class, 'garage_id', 'id');
    }
}
