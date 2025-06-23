@extends('samples')

@section('content')


<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>Garage Details</h5>
    <a href="{{ route('AutoCare.garage_details.create') }}" class="btn btn-primary mb-2">Add New Garage</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($garages->isEmpty())
        <div class="alert alert-warning">
            No brands available. <a href="{{ route('AutoCare.garage_details.create') }}">Create one now</a>.
        </div>
    @else
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($garages as $garage)
                    <tr>
                        <td>{{ $garage->garage_name }}</td>
                        <td>{{ $garage->email }}</td>
                        <td>{{ $garage->mobile }}</td>
                        <td>
                            <span class="badge {{ $garage->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $garage->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('AutoCare.garage_details.edit', $garage->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('AutoCare.garage_details.destroy', $garage->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</div>
@endsection
