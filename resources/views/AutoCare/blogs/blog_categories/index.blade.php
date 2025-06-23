@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
        <h5>Blog Categories</h5>
        <a href="{{ route('AutoCare.blogs.blog_categories.create') }}" class="btn btn-primary mb-3">Create New Category</a>
        <a href="AutoCare/blog" class="btn btn-primary mb-3">Blog</a>

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

                <table class="table table-bordered" id="datable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->category_id }}</td>
                                <td>{{ $category->title }}</td>
                                <td>{{ $category->slug }}</td>
                                <td class="text-center">
                                    <a href="{{ route('AutoCare.blogs.blog_categories.edit', $category->category_id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('AutoCare.blogs.blog_categories.destroy', $category->category_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to Delete this category?')">Delete</button>
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
