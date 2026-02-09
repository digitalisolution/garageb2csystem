@extends('samples')
@section('content')

<h1>Calendar Settings</h1>
<a href="{{ route('calendar.manage') }}">Add New Setting</a>

<table>
    <thead>
        <tr>
            <th>Calendar Name</th>
            <th>Default</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- @dd($settings); -->
    </tbody>
</table>
@endsection