<?php

namespace App\Traits;

trait HasPermissionCheck
{
    public function authorizePermission($permission)
    {
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, "You don't have permission: $permission");
        }
    }
}
