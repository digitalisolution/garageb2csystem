@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <section class="container-fluid">
        <div class="bg-white p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filters</h5>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
                    data-bs-target="#workshopFilters" aria-expanded="true" aria-controls="workshopFilters"
                    id="filterToggleBtn">
                    <span id="filterToggleText">Show Filters</span>
                    <i id="filterToggleIcon" class="fa fa-chevron-up ms-1"></i>
                </button>
            </div>

            <div class="collapse" id="workshopFilters">
                {{ Form::open([
        'url' => 'AutoCare/workshop/search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
        'autocomplete' => 'OFF'
    ]) }}
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Garage Id:</label>
                        {{ Form::text('id', request('id', old('id')), [
        'class' => 'form-control',
        'id' => 'id',
        'placeholder' => 'Job Id'
    ]) }}
                    </div>

                    <!-- Customer Name -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Garage Name:</label>
                        {{ Form::text('name', request('name', old('name')), [
        'class' => 'form-control',
        'name' => 'name',
        'placeholder' => 'Name'
    ]) }}
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Mobile Number:</label>
                        {{ Form::text('mobile', request('mobile', old('mobile')), [ 
        'class' => 'form-control',
        'placeholder' => 'Mobile'
    ]) }}
                    </div>

                    <!-- From Date -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>From Date:</label>
                        {{ Form::date('created_at_from', request('created_at_from', old('created_at_from')), [
        'class' => 'form-control',
        'placeholder' => 'created_at_from' // Removed incorrect attribute name
    ]) }}
                    </div>

                    <!-- To Date -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>To Date:</label>
                        {{ Form::date('created_at_to', request('created_at_to', old('created_at_to')), [
        'class' => 'form-control',
        'placeholder' => 'created_at_to' // Removed incorrect attribute name
    ]) }}
                    </div>

                    <!-- Email -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Email:</label>
                        {{ Form::text('email', request('email', old('email')), [
        'class' => 'form-control',
        'placeholder' => 'Email' // Removed incorrect attribute name
    ]) }}
                    </div>
<div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="status">Status:</label>
                        {{ Form::select('status', ['0' => 'Inactive', '1' => 'Active'], request('status', old('status')), [
        'id' => 'status',
        'class' => 'form-control',
        'placeholder' => 'Select Workshop Status'
    ]) }}
                    </div>
                </div>
                <div class="text-right">
                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                    <!-- Optional: Add a Reset button -->
                    <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>

        <div class="row">
            <!-- left column -->
            <div class="col-md-12 col-sm-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        @if ($errors->any())
                            <ul class="alert alert-danger" style="list-style:none">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <!-- Notifications -->
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

                        @if (session()->has('message.level'))
                            <div class="alert alert-{{ session('message.level') }} alert-dismissible"
                                onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
                                {!! session('message.content') !!}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Garages Detail
                        <a class="btn btn-primary text-center float-right"
                            href="{{ asset('/AutoCare/garages/create') }}">Add Garage</a>
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;">
                        <table id="garageTable" class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="white-space: nowrap">Id</th>
                                    <th style="white-space: nowrap">Name</th>
                                    <th style="white-space: nowrap">Email</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <th align="right">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <style>
            #datable_1_info,
            #datable_1_paginate {
                display: none;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .page-item {
                margin: 2px;
            }

            .dt-buttons {
                margin-left: 200px;
                padding: 0px 20px 25px 20px;
            }
        </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapseElement = document.getElementById('workshopFilters');
            const toggleButton = document.getElementById('filterToggleBtn');
            const toggleIcon = document.getElementById('filterToggleIcon');
            const toggleText = document.getElementById('filterToggleText');

            // Function to update button text and icon based on collapse state
            function updateToggleButton(isVisible) {
                if (isVisible) {
                    toggleIcon.classList.remove('fa-chevron-down');
                    toggleIcon.classList.add('fa-chevron-up');
                } else {
                    toggleIcon.classList.remove('fa-chevron-up');
                    toggleIcon.classList.add('fa-chevron-down');
                }
            }
            collapseElement.addEventListener('show.bs.collapse', function () {
                updateToggleButton(true);
            });

            collapseElement.addEventListener('hide.bs.collapse', function () {
                updateToggleButton(false);
            });
            updateToggleButton(collapseElement.classList.contains('show'));
        });
    </script>
    <script>
        $(document).ready(function () {
            var table = $('#garageTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: 'Blfrtip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                ajax: {
                    url: "{{ route('garages.data') }}",
                    data: function (d) {
                        d.id = $('input[name="id"]').val();
                        d.name = $('input[name="name"]').val();
                        d.email = $('input[name="email"]').val();
                        d.mobile = $('input[name="mobile"]').val();
                        d.status = $('select[name="status"]').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'garages.id' },
                    { data: 'garage_name', name: 'garages.garage_name' },
                    { data: 'garage_email', name: 'garages.garage_email' },
                    { data: 'garage_mobile', name: 'garages.garage_mobile' },
                    { data: 'status_badge', name: 'garages.garage_status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            });
        });
    </script>
   <script>
    $(document).ready(function() {
    $(document).on('click', '.delete-garage', function(e) {
        e.preventDefault();
        
        var garageId = $(this).data('id');
        var garageName = $(this).data('name');
        
        if (confirm('Are you sure you want to delete garage: ' + garageName + '?')) {
            $.ajax({
                url: '/AutoCare/garages/' + garageId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error deleting garage: ' + xhr.responseJSON.message || 'An error occurred');
                }
            });
        }
    });
});
$(document).on('change', '.toggle-status', function() {
    var garageId = $(this).data('id');
    var newStatus = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: '/AutoCare/garages/update-status/' + garageId,
        type: 'POST',
        data: {
            status: newStatus,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#garageTable').DataTable().ajax.reload(null, false);
                alert(response.message);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error: ' + (xhr.responseJSON.message || 'An error occurred'));
        }
    });
});

   </script>
@endsection