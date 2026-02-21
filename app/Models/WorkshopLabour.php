<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopLabour extends Model
{
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $fillable = [
		'labour_id',
		'workshop_id',
		'ref_type',
		'tax_class_id',
		'labour_name',
		'fitting_type',
		'product_type',
		'labour_quantity',
		'labour_price',
	];
	protected $dates = ['deleted_at'];
	
}
