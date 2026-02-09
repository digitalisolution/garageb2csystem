<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
     use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'password_hint',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // public function role()
    // {
    //     return $this->hasOne('App\Role', 'id', 'role_id');
    // }
    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isCustomer()
    {
        return $this->role_id === 2;
    }

    public function user_detail()
    {
        return $this->hasOne('App\Models\UserDetail', 'users_id', 'id');
    }

public function role()
{
    return $this->belongsTo(\App\Models\Role::class, 'role_id');
}

public function hasRole($roleName)
{
    return $this->role && strtolower($this->role->name) === strtolower($roleName);
}


public function hasPermission($permissionName)
{
    return $this->role
        ->permissions
        ->contains('name', $permissionName);
}

}
