<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
	use SoftDeletes;
	// protected $table = 'patient_details';
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	// protected $guarded = ['id', 'created_at', 'updated_at','deleted_at'];
	protected $dates = ['deleted_at'];
	// Workshop.php
	protected $casts = [
		'is_read' => 'boolean',
	];

	public function tyres()
	{
		return $this->hasMany(WorkshopTyre::class, 'workshop_id', 'id')->where('ref_type', 'estimate');
	}
	public function bookings()
	{
		return $this->hasMany(Booking::class, 'workshop_id');
	}
	// public function items()
	// {
	// 	return $this->hasMany(WorkshopTyre::class, 'workshop_id');
	// }
	public function items()
	{
		return $this->hasMany(WorkshopTyre::class, 'workshop_id', 'id')->where('ref_type', 'estimate');
	}


	public function services()
	{
		return $this->hasMany(WorkshopService::class, 'workshop_id', 'id')->where('ref_type', 'estimate');;
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id');
	}

	public function vehicle()
	{
		return $this->belongsTo(VehicleDetail::class, 'vehicle_id');
	}

}
