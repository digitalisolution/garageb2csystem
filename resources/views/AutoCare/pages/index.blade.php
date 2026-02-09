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
    <a href="{{ route('pages.create') }}" class="btn btn-primary mb-3">Add New Page</a>

    <table id="datable" class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>id</th>
                <th>Title</th>
                <th>Slug</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->slug }}</td>
                    <td class="text-center">
                        <span class="badge {{ $page->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $page->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('pages.destroy', $page->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                        </form>
                    </td>
                </tr>
               
            @endforeach
        </tbody>
    </table>
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
