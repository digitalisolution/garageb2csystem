<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
	use SoftDeletes;

	protected $table = 'services'; // Make sure this matches your table name
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $dates = ['deleted_at'];

	// Specify the attributes you want to allow mass assignment for
	protected $fillable = ['service_name', 'service_slug', 'description', 'price', 'other_columns'];
	// Replace with actual columns in your services table


	// Relationship Example if needed
	public function serviceName()
	{
		return $this->hasOne('App\Models\ServiceName');
	}
}

