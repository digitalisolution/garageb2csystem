@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <section class="container-fluid">
        <div class="bg-white p-3 mb-3">
            {{ Form::open([
        'url' => 'AutoCare/estimate/search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
        'autocomplete' => 'OFF'
    ]) }}
            <div class="row">
                <!-- Job/Workshop Id -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Estimate Id:</label>
                    {{ Form::text('id', isset($id) ? $id : old('id'), [
        'class' => 'form-control',
        'id' => 'id',
        'placeholder' => 'Job Id'
    ]) }}
                </div>

                <!-- Customer Name -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Customer Name:</label>
                    {{ Form::text('name', isset($name) ? $name : old('name'), [
        'class' => 'form-control',
        'name' => 'name',
        'placeholder' => 'Name'
    ]) }}
                </div>

                <!-- Mobile Number -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Mobile Number:</label>
                    {{ Form::text('mobile', isset($mobile) ? $namobileme : old('mobile'), [
        'class' => 'form-control',
        'mobile' => 'mobile',
        'placeholder' => 'Mobile'
    ]) }}
                </div>

                <!-- From Date -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>From Date:</label>
                    {{ Form::date('created_at_from', isset($created_at_from) ? $created_at_from : old('created_at_from'), [
        'class' => 'form-control',
        'created_at_from' => 'created_at_from',
        'placeholder' => 'created_at_from'
    ]) }}
                </div>

                <!-- To Date -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>To Date:</label>
                    {{ Form::date('created_at_to', isset($created_at_to) ? $created_at_to : old('created_at_to'), [
        'class' => 'form-control',
        'created_at_to' => 'created_at_to',
        'placeholder' => 'created_at_to'
    ]) }}
                </div>

                <!-- Email -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Email:</label>
                    {{ Form::text('email', isset($email) ? $email : old('email'), [
        'class' => 'form-control',
        'email' => 'email',
        'placeholder' => 'Email'
    ]) }}
                </div>
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="origin">Origin:</label>
                    {{ Form::select('origin', ['website' => 'Website', 'admin' => 'Admin'], isset($origin) ? $origin : old('origin'), [
        'id' => 'origin',
        'class' => 'form-control',
        'placeholder' => 'Select Origin'
    ]) }}
                </div>

                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="status">Estimate Status:</label>
                    {{ Form::select('status', ['booked' => 'Booked', 'completed' => 'Completed', 'failed' => 'Failed', 'pending' => 'Pending'], isset($status) ? $status : old('status'), [
        'id' => 'status',
        'class' => 'form-control',
        'placeholder' => 'Select Workshop Status'
    ]) }}
                </div>

                <!-- Vehicle Registration Number -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Vehicle Registration Number:</label>
                    {{ Form::text('vehicle_reg_number_for_search', isset($vehicle_reg_number_for_search) ? $vehicle_reg_number_for_search : old('vehicle_reg_number_for_search'), [
        'class' => 'form-control',
        'vehicle_reg_number_for_search' => 'vehicle_reg_number_for_search',
        'placeholder' => 'Vehicle Reg No.'
    ]) }}
                </div>
            </div>
            <div class="text-right">
                <input type="submit" name="search" class="btn btn-primary" value="Search">
            </div>
            {{ Form::close() }}
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
        <div class="bg-white p-2 mb-3 rounded border text-center">
            <div class="col-8 m-auto">
                <div class="row">
                    <div class="col-6"><a class="btn btn-primary text-right btn-block text-center"
                            href="{{ asset('/AutoCare/estimate/add') }}">Create New Estimate</a></div>
                </div>
            </div>
        </div>
        @php
            $paid_price = 0;
            $installmentPayment = 0;
            $discount_price = 0;
            $balance_price = 0;
            $grandTotal = 0;
        @endphp
        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Estimate Detail
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;">
                        <table id="" class="table table-hover" style="font-size: 13px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="white-space: nowrap">Estimate Date</th>
                                    <th style="white-space: nowrap">Estimate Id</th>
                                    <th style="white-space: nowrap" style="white-space: nowrap">Customer Name</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <th style="white-space: nowrap">Vehicle Reg. No</th>
                                    <th style="white-space: nowrap">Origin</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <th align="right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workshop as $key => $value)
                                    @php
                                        $due_in = isset($value->due_in) ? date('d/m/Y', strtotime($value->due_in)) : '';
                                        $created_at = isset($value->created_at) ? date('d/m/Y H:i:s', strtotime($value->created_at)) : '';
                                        $due_out = isset($value->due_out) ? date('d/m/Y', strtotime($value->due_out)) : '';
                                        $workshop_date = isset($value->workshop_date) ? date('d/m/Y H:i:s', strtotime($value->workshop_date)) : '';
                                    @endphp
                                    <tr>
                                        <td>{{ $workshop_date }}</td>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>
                                            @if (isset($value->mobile))
                                                {{ $value->mobile }}
                                            @endif
                                        </td>
                                        <td class="text-uppercase">{{ $value->vehicle_reg_number }}</td>
                                        <td><span class="{{ $value->workshop_origin }}">{{ $value->workshop_origin }}</span>
                                        </td>
                                        <td><span class="{{ $value->status }}">{{ $value->status }}</span></td>

                                        <td style="white-space: nowrap;" align="right">
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $value->id }}" type="button"
                                                    class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right btngroup-dropdown"
                                                    aria-labelledby="btnGroupDrop{{ $value->id }}">

                                                       
                                                        <li>
                                                            <a href="{{ url('/AutoCare/estimate/addinvoice/' . $value->id) }}"
                                                                class="dropdown-item btn btn-primary btn-sm">
                                                                <i class="fa fa-upload"></i> Convert to Workshop
                                                            </a>
                                                        </li>

                                                    {{-- View / Edit --}}
                                                    <li>
                                                        <a target="_blank"
                                                            href="{{ url('/AutoCare/estimate/view/' . $value->id) }}"
                                                            class="dropdown-item btn btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i> Est View
                                                        </a>
                                                    </li>

                                                    @if ($value->is_workshop == 1)
                                                     
                                                        <li>
                                                            <a href="{{ url('/AutoCare/estimate/add/' . $value->id) }}"
                                                                class="dropdown-item btn btn-success btn-sm">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                    @endif
                                                     <li>
                                                            <button type="button" class="dropdown-item btn btn-info btn-sm"
                                                                data-toggle="modal" data-target="#emailModal{{ $value->id }}">
                                                                <i class="fa fa-envelope"></i> Email Estimate
                                                            </button>
                                                            @include('AutoCare.workshop.invoice-email-modal', ['invoiceId' => $value->id])
                                                        </li>
                                                        @if ($role_id == 1)
                                                            <li>
                                                                <a href="{{ route('invoice.preview', $value->id) }}" target="_blank"
                                                                    class="dropdown-item btn btn-info btn-sm">
                                                                    <i class="fa fa-eye"></i> Preview PDF
                                                                </a>
                                                            </li>
                                                        @endif

                                                    {{-- Delete --}}
                                                    <li>
                                                        <form action="{{ url('/AutoCare/estimate/trash/' . $value->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this workshop?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item btn btn-danger btn-sm">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Laravel Pagination Links -->
            <div class="pagination-container">
                {{ $workshop->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
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
        </style>
@endsection