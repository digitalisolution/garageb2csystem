@extends('samples')

@section('content')
@if($metasettings)
    <div class="container-fluid">
<div class="bg-white p-3">
        <h3>Edit Template</h3>

        <form action="{{ route('AutoCare.meta-settings.update', $metasettings->setting_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Title</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $metasettings->name }}" required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" class="form-control" id="content" rows="4"
                    required>{{ $metasettings->content }}</textarea>
            </div>

            <!-- Status - radio buttons to set the status (Active/Inactive) -->
            <div class="form-group">
                <label for="status">Status</label><br>
                <label>
                    <input type="radio" name="status" value="1" {{ $metasettings->status == 1 ? 'checked' : '' }} required>
                    Active
                </label>
                <label>
                    <input type="radio" name="status" value="0" {{ $metasettings->status == 0 ? 'checked' : '' }} required>
                    Inactive
                </label>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Meta</button>
        </form>
    </div>
</div>
@else
    <p>Template not found!</p>
@endif
@endsection