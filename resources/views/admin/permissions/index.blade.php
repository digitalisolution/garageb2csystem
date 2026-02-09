@extends('samples')

@section('content')
<section class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row">
        <div class="col-sm-12" id="HideForShowProduct">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Permission Detail
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary text-center float-right"><i class="fa fa-plus"></i> Add Permission</a>
                </div>
                <div class="card-body table-responsive"
                    style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="headingbrand">
                                    <span class="accordion-button bg-light fw-bold px-3 py-3 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ ucfirst($module) }}" aria-expanded="false"><i class="{{ $modulePermissions->first()->icon ?? 'bi bi-grid' }}"></i>
                                        {{ ucfirst($module) }} Module</span>
                                </h6>
                                <div id="{{ ucfirst($module) }}" class="accordion-collapse collapse" style="">
                                    <div class="accordion-body p-1">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($modulePermissions as $p)
                                                <tr>
                                                    <td>{{ $p->id }}</td>

                                                    <td>
                                                        <span class="badge bg-primary">{{ $p->name }}</span>
                                                    </td>

                                                    <td>{{ $p->description ?? '—' }}</td>

                                                    <td align="right">
                                                        <a href="{{ route('permissions.edit', $p->id) }}"
                                                           class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>

                                                        <form action="{{ route('permissions.destroy', $p->id) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Delete permission?')">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
