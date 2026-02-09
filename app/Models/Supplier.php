<?php

namespace App\Models;

use App\Models\Garage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
	use SoftDeletes;
	// protected $table = 'patient_details';
	protected $guarded = ['_token', 'id', 'created_at', 'updated_at', 'deleted_at'];
	protected $fillable = ['garage_id', 'supplier_name', 'mob_num', 'address', 'email', 'api_order_enable', 'api_order_details', 'gstin','website_display_status', 'status', 'import_method', 'ftp_host', 'ftp_user', 'ftp_password', 'ftp_directory', 'file_path'];
	// protected $guarded = ['id', 'created_at', 'updated_at','deleted_at'];
	protected $dates = ['deleted_at'];

	public function products()
	{
		return $this->hasMany(TyresProduct::class, 'supplier_id', 'id');
	}
	public function garage()
	{
	    return $this->belongsTo(Garage::class, 'garage_id');
	}
}

