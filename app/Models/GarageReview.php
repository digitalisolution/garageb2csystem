<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarageReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'garage_id',
        'customer_id',
        'rating',
        'review',
        'approved',
    ];

    public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id');
	}
}
