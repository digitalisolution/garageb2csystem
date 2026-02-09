@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h5>Brands Management</h5>
            <a href="{{ route('brand.create') }}" class="btn btn-primary mb-3">Add New Brand</a>

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

            @if ($brands->isEmpty())
                <div class="alert alert-warning">
                    No brands available. <a href="{{ route('brand.create') }}">Create one now</a>.
                </div>
            @else
                <table class="table table-bordered" id="datable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $brand)
                            <tr>
                                <td>{{ $brand->brand_id }}</td>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->slug }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $brand->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $brand->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('brand.edit', $brand->brand_id) }}"
                                        class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('brand.destroy', $brand->brand_id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this brand?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <script>
$(document).ready(function () {
    if (!$.fn.DataTable.isDataTable('#datable')) { // Check if the table is already initialized
        $('#datable').DataTable({
            dom: 'lBfrtip', // Add 'l' for the length menu on the left
            buttons: [
                'csv', 'excel', 'pdf', 'print' // Include export buttons
            ],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Example: Calculate the total of the second column
                var total = api.column(1, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                // Update footer
                $(api.column(1).footer()).html(total.toFixed(2));
            }
        });
    }
});
    </script>
    @endsection