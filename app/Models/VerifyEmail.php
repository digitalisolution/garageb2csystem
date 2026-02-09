<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyEmail extends Model
{
	use HasFactory;

	protected $table = 'verify_emails'; // Table name

	protected $primaryKey = 'id'; // Primary key

	public $timestamps = false; // Since we use 'created_on', disable Laravel timestamps

	protected $fillable = [
		'email_to',
		'email_from',
		'to_verified',
		'status',
		'ip',
		'created_on',
	];

	protected $casts = [
		'to_verified' => 'boolean',
		'status' => 'boolean',
		'created_on' => 'datetime',
	];
}
