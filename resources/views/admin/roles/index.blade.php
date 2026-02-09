@extends('samples')

@section('content')
<section class="container-fluid">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Roles Detail
                        <a href="{{ route('roles.create') }}" class="btn btn-primary text-center float-right"><i class="fa fa-plus"></i> Add Role</a>
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
                        <table id="datable" class="table table-bordered table-sm">
                            <thead class="thead-dark"><tr><th>ID</th><th>Name</th><th>Permissions</th><th width="110" class="text-center">Actions</th></tr></thead>
                            <tbody>
                                @foreach($roles as $role)
                                @if($role->id != 1 || auth()->user()->role_id == 1 )
                                <tr>
                                    <td>
                                    {{ $role->id }}
                                    </td>
                                    <td>{{ $role->role_name }}</td>
                                    <td>
                                        @foreach($role->permissions as $p)
                                            <span class="badge bg-secondary">{{ $p->name }}</span>
                                        @endforeach
                                    </td>
                                    <td align="center">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline-block;">
                                            @csrf 
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete role?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                 @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection
