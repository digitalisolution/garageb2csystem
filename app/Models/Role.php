<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    protected $fillable = ['role_name', 'guard_name','is_active'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
