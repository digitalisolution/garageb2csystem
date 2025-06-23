<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class tyre_brands extends Model
{
    use HasFactory;
    protected $table = 'tyre_brands';
    protected $primaryKey = 'brand_id'; // Specify the primary key
    public $incrementing = true; // Indicates if the primary key is auto-incrementing
    protected $keyType = 'int'; // Primary key type (int in this case)
    // Specify the fillable field
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
        'bannerimage'
    ];


    // Relationship for parent pages
    public function brand()
    {
        return $this->findOrFail(tyre_brands::class, 'brand_id');
    }
    public function brandName()
    {
        return $this->belongsTo(tyre_brands::class, 'brand_id', 'tyre_brand_id');
    }

}
