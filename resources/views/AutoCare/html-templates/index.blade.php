@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>HTML Templates</h5>

    <!-- Success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('AutoCare.html-templates.create') }}" class="btn btn-primary mb-3">Create New Template</a>

    <table id="datable_1" class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Template Type</th>
                <th>Status</th>
                <th>Sort Order</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($htmlTemplates as $template)
                <tr>
                    <td>{{ $template->id }}</td>
                    <td>{{ $template->title }}</td>
                    <td>{{ $template->template_type }}</td>
                    <td>{{ $template->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $template->sort_order }}</td> <!-- Added sort_order -->
                    <td class="text-center">
                        <a href="{{ route('AutoCare.html-templates.edit', $template->id) }}"
                            class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('AutoCare.html-templates.destroy', $template->id) }}" method="POST"
                            style="display:inline;">
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
@endsection