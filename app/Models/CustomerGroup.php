<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'due_date_option',
        'manual_due_date',
        'product_type',
    ];

    protected $casts = [
        'manual_due_date' => 'date',
        'product_type' => 'array',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_group_id');
    }
}
