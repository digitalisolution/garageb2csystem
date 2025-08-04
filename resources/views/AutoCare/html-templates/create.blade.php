@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h5>Create New Template</h5>
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

            <form action="{{ route('AutoCare.html-templates.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control" id="title" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea name="content" class="form-control" id="content" rows="4" required></textarea>
                </div>

                <!-- Template Type - input field instead of select -->
                <div class="form-group">
                    <label for="template_type">Template Type</label>
                    <input type="text" name="template_type" class="form-control" id="template_type" required>
                </div>

                <!-- Status - added as a radio button group or a select -->
                <div class="form-group">
                    <label for="status">Status</label><br>
                    <label>
                        <input type="radio" name="status" value="1" required> Active
                    </label>
                    <label>
                        <input type="radio" name="status" value="0" required> Inactive
                    </label>
                </div>

                <!-- Sort Order - a number input for sorting -->
                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" id="sort_order" required min="1">
                </div>

                <button type="submit" class="btn btn-success mt-3">Create Template</button>
            </form>
        </div>
    </div>
@endsection