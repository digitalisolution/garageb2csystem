<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'module',
        'name',
        'guard_name',
        'description',
        'icon'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}

