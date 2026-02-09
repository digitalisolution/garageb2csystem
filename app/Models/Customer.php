<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Support\Str;

class Customer extends Authenticatable implements CanResetPassword
{
	use Notifiable;

	use SoftDeletes;
	use HasFactory;

	protected $table = 'customers'; // Ensure correct table name
	protected $primaryKey = 'id'; // Define primary key

	protected $fillable = [
		'customer_name',
		'customer_last_name',
		'customer_group_id',
		'customer_contact_number',
		'customer_alt_number',
		'customer_email',
		'customer_address',
		'customer_gstin',
		'company_name',
		'company_website',
		'billing_address_street',
		'billing_address_city',
		'billing_address_postcode',
		'billing_address_county',
		'billing_address_country',
		'shipping_address_street',
		'shipping_address_city',
		'shipping_address_postcode',
		'shipping_address_county',
		'shipping_address_country',
		'same_as_shipping_address',
		'password'
	];

	protected $hidden = [
		'password',
		'remember_token',
	];
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $dates = ['deleted_at'];
	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'customer_id');
	}

	public function group()
	{
		return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
	}

	public function Workshop()
	{
		return $this->hasMany(Workshop::class, 'customer_id');
	}
	public function customerVehicles()
	{
		return $this->hasMany(Vehicle::class, 'customer_id');
	}
	public function vehicleDetails()
	{
		return $this->hasMany(VehicleDetail::class);
	}
	public function vehicles()
	{
		return $this->belongsToMany(VehicleDetail::class, 'customer_vehicle')->withTimestamps();
	}
	public function getEmailForPasswordReset()
	{
		return $this->customer_email;
	}
	/**
	 * Send the password reset notification.
	 *
	 * @param string $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new CustomerResetPasswordNotification($token));
	}
}
