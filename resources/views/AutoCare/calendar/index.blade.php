@extends('samples')

@section('content')
<section class="container-fluid">
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
        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Calendar Settings
                        <a href="{{ route('calendar.create') }}" class="btn btn-primary text-center float-right"><i class="fa fa-plus"></i> Add Calendar Settings</a>
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
                        <table id="datable" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Calendar Name</th>
                                    <th>Garage Name</th>
                                    <th>Calendar Type</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($settings as $setting)
                                    <tr>
                                        <td>{{ $setting->calendar_name }}</td>
                                         <td>{{ $setting->garage->garage_name ?? 'N/A' }}</td>
                                        <td>{{ strtoupper(str_replace('calendar_',' ',$setting->calendar_type))}}</td>
                                        <td>{{ $setting->default ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <a href="{{ route('calendar.edit', $setting->calendar_setting_id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('calendar.destroy', $setting->calendar_setting_id) }}" method="POST"
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
            </div>
        </div>
</section>
@endsection