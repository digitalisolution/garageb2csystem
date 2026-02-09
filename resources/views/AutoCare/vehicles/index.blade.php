@extends('samples')
@section('content')
    <section class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Vehicle Details
                        <a href="{{ route('AutoCare.vehicles.create') }}" class="btn btn-primary text-center float-right"><i class="fa fa-plus"></i> Add Vehicle</a>
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
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
                                        <a href="{{ route('AutoCare.vehicles.edit', $vehicle->id) }}"
                                            class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('AutoCare.vehicles.destroy', $vehicle->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection