<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tyre_brands extends Model
{
    use HasFactory;

    // Default connection; overridden dynamically below
    protected $connection = 'mysql_vehicle_details';

    /**
     * Dynamically return the correct connection name based on host or CLI context.
     */
    public function getConnectionName()
    {
        // $sharedDomains = ['www.digitalideasltd.in', 'b2b.garage-automation.com'];
        $sharedDomains = [];

        // CLI (schedule/command)
        if (app()->runningInConsole()) {
            $cliHost = $_SERVER['APP_FAKE_HOST'] ?? null;

            if ($cliHost && in_array($cliHost, $sharedDomains)) {
                return 'mysql_vehicle_details';
            }

            return config('database.default'); // fallback
        }

        // Web requests
        $host = request()->getHost();
        if (in_array($host, $sharedDomains)) {
            return 'mysql_vehicle_details';
        }

        return config('database.default'); // fallback
    }

    /**
     * Dynamically resolve table name based on host (web or CLI).
     */
    public function getTable()
    {
        if (app()->runningInConsole()) {
            $cliHost = $_SERVER['APP_FAKE_HOST'] ?? null;

            return match ($cliHost) {
                // 'b2b.garage-automation.com'     => 'tyre_brands_gloucester',
                // 'www.digitalideasltd.in'        => 'tyre_brands_gloucester',
                default                         => 'tyre_brands',
            };
        }

        $host = request()->getHost();

        return match ($host) {
            // 'b2b.garage-automation.com'     => 'tyre_brands_gloucester',
            // 'www.digitalideasltd.in'        => 'tyre_brands_gloucester',
            default                         => 'tyre_brands',
        };
    }

    protected $primaryKey = 'brand_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'brand_id',
        'slug',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'promoted',
        'promoted_text',
        'image',
        'budget_type',
        'recommended_tyre',
        'sort_order',
        'product_type',
        'bannerimage',
    ];

    // Relationships
    public function brand()
    {
        return $this->findOrFail(tyre_brands::class, 'brand_id');
    }

    public function brandName()
    {
        return $this->belongsTo(tyre_brands::class, 'brand_id', 'tyre_brand_id');
    }
}
