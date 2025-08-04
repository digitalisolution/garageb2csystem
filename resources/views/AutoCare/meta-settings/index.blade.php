@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h3>Meta Settings</h3>

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

            <a href="{{ route('AutoCare.meta-settings.create') }}" class="btn btn-primary mb-3">Create New Meta</a>

            <table id="datable" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metasettings as $template)
                        <tr>
                            <td>{{ $template->setting_id }}</td>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->content }}</td>
                            <td>{{ $template->status ? 'Active' : 'Inactive' }}</td>
                            <td class="text-center no-wrap">
                                <a href="{{ route('AutoCare.meta-settings.edit', $template->setting_id) }}"
                                    class="btn btn-warning btn-sm">Edit</a>

                                <form action="{{ route('AutoCare.meta-settings.destroy', $template->setting_id) }}"
                                    method="POST" style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this meta setting?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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