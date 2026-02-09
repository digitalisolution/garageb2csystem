<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\HasPermissionCheck;
use DB;
use App\Models\Permission;

class PermissionController extends Controller
{
    
    use HasPermissionCheck;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || auth()->user()->role_id != 1) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $this->authorizePermission('permission.view');
        $permissions = Permission::orderBy('module')->get()->groupBy('module');

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $this->authorizePermission('permission.create');
        $modules = DB::table('modules')->orderBy('label')->get();
        return view('admin.permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|unique:permissions,name',
            'module'      => 'required',
            'description' => 'nullable',
            'icon'        => 'nullable'
        ]);

        $module = $request->module;

        if ($request->is_custom == "1") {

            $module = strtolower($request->custom_module);

            DB::table('modules')->updateOrInsert(
                ['name' => $module],
                [
                    'label' => ucfirst($module),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        Permission::create([
            'module'      => $module,
            'guard_name'  => $module,
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon,
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        $this->authorizePermission('permission.edit');
         $modules = DB::table('modules')->orderBy('label')->get();
        return view('admin.permissions.edit', compact('permission','modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name'        => 'required|unique:permissions,name,' . $permission->id,
            'module'      => 'required',
            'description' => 'nullable',
            'icon'        => 'nullable'
        ]);

        $permission->update([
            'module'      => $request->module,
            'guard_name'      => $request->module,
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon,
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $this->authorizePermission('permission.delete');
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
