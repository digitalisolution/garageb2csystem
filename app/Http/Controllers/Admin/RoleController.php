<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\HasPermissionCheck;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    
    use HasPermissionCheck;
     public function __construct()
    {
        $this->middleware('auth');
 
    }
    private function protectSuperAdmin(Role $role)
{
    // If target role is Super Admin (id = 1)
    if ($role->id == 1 && auth()->user()->role_id != 1) {
        abort(403, 'You cannot modify Super Admin role.');
    }
}

    public function index()
    {
        $this->authorizePermission('role.view');
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
         $this->authorizePermission('role.create');
        $permissions = Permission::orderBy('module', 'asc')
            ->get()
            ->groupBy('guard_name');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required',
            'guard_name' => 'required',
            'status' => 'required',
        ]);

        $role = Role::create([
            'role_name' => $request->role_name,
            'guard_name' => $request->guard_name,
            'is_active' => $request->status
        ]);

        // Attach permissions
        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created.');
    }

    public function edit($id)
    {
         $this->authorizePermission('role.edit');
         $role = Role::findOrFail($id);
         $this->protectSuperAdmin($role);
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        $permissions = Permission::orderBy('name', 'asc')
            ->get()
            ->groupBy('guard_name');

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'role_name' => 'required',
            'guard_name' => 'required',
            'status' => 'required',
        ]);

        $role->update([
            'role_name' => $request->role_name,
            'guard_name' => $request->guard_name,
            'is_active' => $request->status
        ]);

        // Sync permissions
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated.');
    }


    public function destroy($id)
    {
         $this->authorizePermission('role.delete');
        $role = Role::findOrFail($id);
        $this->protectSuperAdmin($role);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }
}
