@extends('samples')

@section('content')
@php
    $actions = ['create', 'delete', 'edit', 'view', 'buy'];
@endphp
<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-dark text-white p-2">
            <h5 class="mb-0">Edit Role</h5>
        </div>

        <div class="card-body p-3">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Role Name -->
              
<div class="row">
 <div class="col-4 mb-1">
                    <label class="form-label fw-semibold">Role Name</label>
                    <input type="text" name="role_name" class="form-control"
                        value="{{ old('role_name', $role->role_name) }}" placeholder="Enter role name">
                </div>
                 <div class="col-4 mb-1">
                    <label class="form-label fw-semibold">Guard Name</label>
                    <input type="text" name="guard_name" class="form-control"
                        value="{{ old('guard_name', $role->guard_name) }}" placeholder="Enter role name">
                </div>
                 <div class="col-4 mb-1">
                    <label class="form-label fw-semibold">Status</label>
                    <input type="text" name="status" class="form-control"
                        value="{{ old('status', $role->is_active) }}" placeholder="Enter role name">
                </div>
</div>
                <!-- Permissions Box -->
                <div class="mt-3">
                    <h6 class="form-label fw-bold bg-light border rounded-1 p-2">Assign Permissions</h6>

                       <div id="permissionsAccordion">
                        @foreach($permissions as $module => $modulePermissions)

                    <div class="permissions_widget">
                        <div class="item">
                        <h6 class="accordion-header" id="heading{{ $module }}">
                            <span class="fw-bold" style="font-size:90%;font-weight:500;">
                                {{ ucfirst($module) }} Module: 
                            </span>
                        </h6>
                    </div>
                    @php 
                    @endphp
                        @foreach($modulePermissions as $perm)
                            <div class="item">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $perm->id }}" id="p{{ $perm->id }}" 
                                    @isset($rolePermissions)
                                    @if(in_array($perm->id, $rolePermissions)) checked @endif
                                    @endisset >
                                    <label class="form-check-label pl-1" for="p{{ $perm->id }}">
                                        {{ ucfirst(str_replace(array($perm->module, '.'), '', $perm->name)) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach

                    </div>


                    @endforeach
                </div>
                </div>

                <!-- Submit -->
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4">Back</a>
                    <button type="submit" class="btn btn-primary px-4">Update Role</button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
