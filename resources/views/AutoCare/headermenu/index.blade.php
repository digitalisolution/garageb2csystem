@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h5>Pages Management</h5>
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
            <a href="{{ route('headermenu.create') }}" class="btn btn-primary mb-3">Add New Page</a>

            <table id="datable_1" class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Sort Order</th>
                        <th>Parent Type</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>{{ $page->sort }}</td>
                            <td>{{ $page->parent_type }}&mdash;</td>
                            <td>{{ $page->title }}</td>
                            <td>{{ $page->slug }}</td>
                            <td class="text-center">
                                <span class="badge {{ $page->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $page->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('headermenu.edit', $page->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('headermenu.destroy', $page->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Display Child Pages -->
                        @if ($page->children)
                            @foreach ($page->children as $child)
                                <tr>
                                    <td>{{ $child->sort }}</td>
                                    <td>{{ $child->parent_type }}</td>
                                    <td>&mdash; {{ $child->title }}</td>
                                    <td>{{ $child->slug }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $child->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $child->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('headermenu.edit', $child->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('headermenu.destroy', $child->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
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