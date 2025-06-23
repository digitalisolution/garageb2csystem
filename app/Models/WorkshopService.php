<?php

namespace App\Models;
use App\tblitems;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopService extends Model
{
	// use SoftDeletes;
	// protected $table = 'patient_details';
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $fillable = [
		'service_type_id',
		'service_id',
		'workshop_id',
		'tax_class_id',
		'service_name',
		'fitting_type',
		'product_type',
		'service_quantity',
		'service_price',
	];
	// protected $guarded = ['id', 'created_at', 'updated_at','deleted_at'];
	protected $dates = ['deleted_at'];
	public function items()
	{
		return $this->hasMany(tblitems::class);
	}
}
