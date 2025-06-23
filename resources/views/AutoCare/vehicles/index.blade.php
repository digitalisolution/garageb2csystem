@extends('samples')

@section('content')
<div class="container">
    <h1>Vehicle Details</h1>
    <a href="{{ route('AutoCare.vehicles.create') }}" class="btn btn-primary mb-3">Add New Vehicle</a>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <table id="datable_1" class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Registration Number</th>
                <th>Category</th>
                <th>Make</th>
                <th>Model</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vehicles as $vehicle)
            <tr>
                <td>{{ $vehicle->id }}</td>
                <td>{{ $vehicle->vehicle_reg_number }}</td>
                <td>{{ $vehicle->vehicle_category }}</td>
                <td>{{ $vehicle->vehicle_make }}</td>
                <td>{{ $vehicle->vehicle_model }}</td>
                <td>{{ $vehicle->vehicle_year }}</td>
                <td>
                    <a href="{{ route('AutoCare.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('AutoCare.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection