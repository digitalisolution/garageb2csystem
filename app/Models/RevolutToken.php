<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevolutToken extends Model
{
    protected $fillable = ['mode', 'access_token', 'refresh_token', 'access_token_expires_at', 'refresh_token_expires_at'];
}