@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3">
    <h3>Create New Meta</h3>

    <form action="{{ route('AutoCare.meta-settings.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Title</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" class="form-control" id="content" rows="4" required></textarea>
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

        <button type="submit" class="btn btn-primary mt-3">Create Meta</button>
    </form>
</div>
</div>
@endsection