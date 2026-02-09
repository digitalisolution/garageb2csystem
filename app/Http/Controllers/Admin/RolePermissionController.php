<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    public function edit($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $permissions = Permission::all();
        return view('admin.role_has_permissions.edit', compact('role','permissions'));
    }

    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($request->permissions ?? []);
        return redirect()->route('roles.index')->with('success','Role permissions updated');
    }
}
