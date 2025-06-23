<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaSettings extends Model
{
    use HasFactory;
    protected $primaryKey = 'setting_id';
    protected $table = 'settings';
    protected $fillable = ['setting_id', 'name', 'content', 'status'];
}
