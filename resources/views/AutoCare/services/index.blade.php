@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h5>Services Management</h5>
            <a href="{{ route('services.create') }}" class="btn btn-primary mb-3">Add New service</a>
             @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <table class="table table-bordered" id="datable_1">
                <thead class="thead-dark">
                    <tr>
                        <th>Service id</th>
                        <th>Service Name</th>
                        <th>Garage Name</th>
                        <th>Slug</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>{{ $service->service_id }}</td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->garage->garage_name }}</td>
                            <td>{{ $service->slug }}</td>
                            <td class="text-center">{{ $service->cost_price }}</td>
                            <td class="text-center">
                                <span class="badge {{ $service->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $service->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td align="center">
                                <a href="{{ route('services.edit', $service->service_id) }}"
                                    class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('services.destroy', $service->service_id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this service?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Display Child services -->
                        @if ($service->children)
                            @foreach ($service->children as $child)
                                <tr>
                                    <td>{{ $child->sort }}</td>
                                    <td>&mdash; {{ $child->title }}</td>
                                    <td>{{ $child->slug }}</td>
                                    <td>
                                        <span class="badge {{ $child->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $child->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('services.edit', $child->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('services.destroy', $child->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this service?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection