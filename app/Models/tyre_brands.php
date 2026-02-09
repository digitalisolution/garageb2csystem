<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tyre_brands extends Model
{
    use HasFactory;

    protected $connection = 'mysql_vehicle_details';

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

        if (
            in_array($host, $this->gloucesterDomains) ||
            in_array($host, $this->tyrelabDomains)
        ) {
            return 'mysql_vehicle_details';
        }

        return config('database.default');
    }

    public function getTable()
    {
        $host = $this->resolveHost();

        return match (true) {
            in_array($host, $this->gloucesterDomains) => 'tyre_brands_gloucester',
            in_array($host, $this->tyrelabDomains)    => 'tyre_brands_tyrelab',
            default                                   => 'tyre_brands',
        };
    }

    protected $primaryKey = 'brand_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'name',
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
}
