@extends('samples')
@section('content')

<h1>Calendar Settings</h1>
<a href="{{ route('calendar.create') }}">Add New Setting</a>

<table>
    <thead class="thead-dark">
        <tr>
            <th>Calendar Name</th>
            <th>Default</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($settings as $setting)
            <tr>
                <td>{{ $setting->calendar_name }}</td>
                <td>{{ $setting->default ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('calendar.edit', $setting->calendar_setting_id) }}">Edit</a>
                    <form action="{{ route('calendar.destroy', $setting->calendar_setting_id) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection