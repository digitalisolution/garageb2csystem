<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopPart extends Model
{

	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $fillable = [
		'part_id',
		'workshop_id',
		'ref_type',
		'tax_class_id',
		'part_name',
		'part_content',
		'fitting_type',
		'product_type',
		'part_quantity',
		'part_price',
	];

	protected $dates = ['deleted_at'];
	
}
