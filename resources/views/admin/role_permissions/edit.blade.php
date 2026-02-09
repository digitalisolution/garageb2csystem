@extends('samples')

@section('content')
<div class="container">
    <h1>Edit Permissions for Role: {{ $role->name }}</h1>

    <form action="{{ url('roles/'.$role->id.'/permissions') }}" method="POST">
        @csrf
        <div class="mb-3">
            @foreach($permissions as $perm)
                <label class="me-2">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                        {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                    {{ $perm->name }}
                </label>
            @endforeach
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
