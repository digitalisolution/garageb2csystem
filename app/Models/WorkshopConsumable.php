<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopConsumable extends Model
{

	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $fillable = [
		'consumable_id',
		'workshop_id',
		'ref_type',
		'tax_class_id',
		'consumable_name',
		'consumable_content',
		'fitting_type',
		'product_type',
		'consumable_quantity',
		'consumable_price',
	];

	protected $dates = ['deleted_at'];
	
}
