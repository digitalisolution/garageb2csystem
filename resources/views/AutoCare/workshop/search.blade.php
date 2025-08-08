@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <section class="container-fluid">
        <div class="bg-white p-3 mb-3">
            {{ Form::open([
        'url' => 'AutoCare/workshop/search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
        'autocomplete' => 'OFF'
    ]) }}
            <div class="row">
                <!-- Job/Workshop Id -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label>Job/Workshop Id:</label>
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
                    <label for="status">Workshop Status:</label>
                    {{ Form::select('status', ['booked' => 'Booked', 'completed' => 'Completed', 'failed' => 'Failed', 'pending' => 'Pending'], isset($status) ? $status : old('status'), [
        'id' => 'status',
        'class' => 'form-control',
        'placeholder' => 'Select Workshop Status'
    ]) }}
                </div>

                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="payment_method">Payment Method:</label>
                    {{ Form::select('payment_method', ['pay_at_fitting_center' => 'Pay at Center', 'dojo' => 'dojo', 'global_payment' => 'Global Pay'], isset($payment_method) ? $payment_method : old('payment_method'), [
        'id' => 'payment_method',
        'class' => 'form-control',
        'placeholder' => 'Select Payment Method'
    ]) }}
                </div>

                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="is_void">Void Invoices:</label>
                    {{ Form::select('is_void', ['1' => 'Yes', '0' => 'No'], isset($is_void) ? $is_void : old('is_void'), [
        'id' => 'is_void',
        'class' => 'form-control',
        'placeholder' => 'Search Void Invoices'
    ]) }}
                </div>

                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="payment_status">Payment Status:</label>
                    {{ Form::select('payment_status', ['1' => 'Paid', '0' => 'Unpaid', '3' => 'Partial'], isset($payment_status) ? $payment_status : old('payment_status'), [
        'id' => 'payment_status',
        'class' => 'form-control',
        'placeholder' => 'Select payment status'
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
                            href="{{ asset('/AutoCare/workshop/add') }}">Create New Workshop</a></div>
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
                        <i class="fa fa-align-justify"></i> Workshop Detail
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;">
                        <table id="" class="table table-hover" style="font-size: 13px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="white-space: nowrap">Workshop Date</th>
                                    <th style="white-space: nowrap">Job Id</th>
                                    <th style="white-space: nowrap" style="white-space: nowrap">Customer Name</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <th style="white-space: nowrap">Vehicle Reg. No</th>
                                    <th style="white-space: nowrap">Payment Method</th>
                                    <th style="white-space: nowrap">Amount Due</th>
                                    <th style="white-space: nowrap">Grand Total</th>
                                    <th style="white-space: nowrap">Payment Status</th>
                                    <th style="white-space: nowrap">Origin</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <th style="white-space: nowrap">Invoice Convert</th>
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
                                   <tr class="{{ ($value->is_void ?? false) || ($value->invoice->is_void ?? false) ? 'table-danger' : '' }}">
                                        <td>{{ $workshop_date }}</td>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>
                                            @if (isset($value->mobile))
                                                {{ $value->mobile }}
                                            @endif
                                        </td>
                                        <td class="text-uppercase">{{ $value->vehicle_reg_number }}</td>
                                        @php
                                            $grandTotal += $value->grandTotal;
                                        @endphp
                                        <td>{{ strtoupper(str_replace('_', ' ', $value->payment_method)) }}</td>
                                        <td>£{{ number_format($value->balance_price, 2, '.', '') }}</td>
                                        <td>£{{ number_format($value->grandTotal, 2, '.', '') }}</td>
                                        <td>
                                            <span
                                                class="{{ $value->payment_status == 1 ? 'Paid' : ($value->payment_status == 3 ? 'Partially' : 'Unpaid') }}">
                                                {{ $value->payment_status == 1 ? 'Paid' : ($value->payment_status == 3 ? 'Partially' : 'Unpaid') }}
                                            </span>
                                        </td>
                                        <td><span class="{{ $value->workshop_origin }}">{{ $value->workshop_origin }}</span>
                                        </td>
                                        <td><span class="{{ $value->status }}">{{ $value->status }}</span></td>
                                        <td>
                                            <span
                                                class="{{ $value->is_converted_to_invoice == 1 ? 'invoice' : ($value->is_converted_to_invoice == 0 ? 'workshop' : 'workshop') }}">
                                                {{ $value->is_converted_to_invoice == 1 ? 'invoice' : ($value->is_converted_to_invoice == 0 ? 'workshop' : 'workshop') }}
                                            </span>
                                        </td>
                                        <td style="white-space: nowrap;" align="right">
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $value->id }}" type="button"
                                                    class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu btngroup-dropdown"
                                                    aria-labelledby="btnGroupDrop{{ $value->id }}">
                                                    @if ($value->is_converted_to_invoice == 1)
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $value->id }}"
                                                                class="dropdown-item btn btn-warning btn-sm">
                                                                <i class="fa fa-pencil" aria-hidden="true"></i> Update Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a target="_blank"
                                                                href="{{ url('/') }}/AutoCare/workshop/invoice/{{ $value->id }}"
                                                                class="dropdown-item btn btn-primary btn-sm">
                                                                <i class="fa fa-eye"></i> View Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item btn btn-info btn-sm"
                                                                data-toggle="modal" data-target="#emailModal{{ $value->id }}">
                                                                <i class="fa fa-envelope"></i> Email Invoice
                                                            </button>
                                                        </li>
                                                        @if ($role_id == 1)
                                                            <li>
                                                                <a href="{{ route('invoice.preview', $value->id) }}" target="_blank"
                                                                    class="dropdown-item btn btn-info btn-sm">
                                                                    <i class="fa fa-eye"></i> Preview PDF
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($value->is_void === 0)
                                                        <li>
                                                        <form action="{{ url('/AutoCare/workshop/void/' . $value->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to void this workshop?');">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                                <i class="fa fa-remove"></i> Void Invoice
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                    @else
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $value->id }}"
                                                                class="dropdown-item btn btn-primary btn-sm">
                                                                <i class="fa fa-upload"></i> Convert to Invoice
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <a data-toggle="modal" id="{{ $value->id }}"
                                                            data-target="#workshopDiscount"
                                                            data-balance-total="{{ $value->balance_price }}"
                                                            class="dropdown-item btn btn-success openDiscountModelForWorkshop btn-sm">
                                                            <i class="fa fa-money" aria-hidden="true"></i> Discount
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a data-toggle="modal" id="{{ $value->id }}"
                                                            data-target="#workshopPayment"
                                                            class="dropdown-item btn btn-success openPayentModelForWorkshop btn-sm"
                                                            data-grand-total="{{ $value->balance_price }}">
                                                            <i class="fa fa-money" aria-hidden="true"></i> Receive Payment
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a target="_blank"
                                                            href="{{ url('/') }}/AutoCare/workshop/view/{{ $value->id }}"
                                                            class="dropdown-item btn btn-primary btn-sm">
                                                            <i class="fa fa-eye"></i> Job View
                                                        </a>
                                                    </li>

                                                    @if ($value->is_workshop == 1)
                                                        <li>
                                                            <a target="_blank"
                                                                href="{{ url('/') }}/AutoCare/workshop/payment_history/{{ $value->id }}"
                                                                class="dropdown-item btn btn-danger btn-sm" title="Payment History">
                                                                <i class="fa fa-eye"></i> Payment History
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/workshop/add/{{ $value->id }}"
                                                                class="dropdown-item btn btn-success btn-sm">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="{{ url('/') }}/AutoCare/sale/edit/{{ $value->id }}"
                                                                class="dropdown-item btn btn-success btn-sm">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a href="#"
                                                            class="dropdown-item btn btn-success btn-sm open-activity-log-modal"
                                                            data-id="{{ $value->id }}">
                                                            <i class="fa fa-eye" aria-hidden="true"></i> Activity Log
                                                        </a>
                                                    </li>

                                                    @if ($role_id == 1)    
                                                    <li>
                                                        <form action="{{ url('/AutoCare/workshop/trash/' . $value->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this workshop?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                                <i class="fa fa-remove"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('AutoCare.workshop.invoice-email-modal', ['invoiceId' => $value->id])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" id="HideForShowProduct2" style="display: none">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Workshop Id</th>
                                    <th>Spare Name</th>
                                    <th>Product Quantity</th>
                                    <th>Product Price</th>
                                    <th align="right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productDetail">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><input type="button" id="closeProdultDetail" class="btn btn-primary"
                                            value="close"></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="card-footer"></div>
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
       
        <!-- Modal for payment : start-->
        <div class="modal fade" id="workshopPayment" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Customer Payment For Workshop ID: <span id="paymentWorkshopId"></span></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Payment Form -->
                        <form id="formId" action="{{ url('/') }}/ajax/submitCustomerPaymentDetail" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="paymentLogId" value="">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Credit/Debit</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Payment Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Get current UK datetime formatted for datetime-local input
                                        $ukNow = \Carbon\Carbon::now('Europe/London')->format('Y-m-d\TH:i');

                                        // Determine the value to show for payment_date
                                        $paymentDateValue = old('payment_dateForWorkshop', isset($log) ? $log->payment_date : $ukNow);
                                    @endphp

                                    <tr>
                                        <td>
                                            <select name="creditDebitForWorkshop" class="form-control">
                                                <!-- <option value="0">Credit</option> -->
                                                <option value="1" selected>Debit</option>
                                            </select>
                                            <input type="hidden" name="workshopIdForPayment">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" step="any" name="amountForWorkshop"
                                                value="{{ old('amountForWorkshop', isset($log) ? $log->amount : '') }}">
                                        </td>
                                        <td>
                                            <input type="datetime-local" class="form-control" id="paymentDate"
                                                name="payment_dateForWorkshop" value="{{ $paymentDateValue }}">
                                        </td>
                                        <td>
                                            {{ Form::select('payment_typeForWorkshop', ['2' => 'By Card', '1' => 'By Cash', '3' => 'By Cheque'], old('payment_typeForWorkshop', isset($log) ? $log->payment_type : null), ['class' => 'form-control']) }}
                                        </td>
                                        <td>
                                            {{ Form::textarea('commentsForWorkshop', old('commentsForWorkshop', isset($log) ? $log->comments : null), ['class' => 'form-control', 'id' => 'comments', 'style' => 'height: 40px;']) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><input type="button" id="paymentForWorkshop" class="btn btn-sm btn-success"
                                                value="Submit"></td>
                                        <td><button type="reset" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i>
                                                Reset</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>

                        <!-- Payment Logs Table -->
                        <h5>Previous Payment Logs</h5>
                        <table class="table" id="paymentLogsTable">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Payment Type</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Payment logs will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for payment :end -->

        <!-- Modal for Activity Log :start -->
        <!-- Modal (outside the loop) -->
        <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h5 class="modal-title" id="activityModalLabel">Workshop Activity Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="activity-log-content">
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Activity Log :end -->

        <!-- Modal for discount : start-->
        <div class="modal fade" id="workshopDiscount" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Customer Discount For Workshop ID:<span id="discountWorkshopId"></span></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <table class="table">
                            <thead>
                                <tr>
                                    <th align="center">Discount Type</th>
                                    <th align="center">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <!-- Dropdown for selecting discount type -->
                                        <select class="form-control" id="discountType" name="discountType">
                                            <option value="amount">Fixed Amount</option>
                                            <option value="percentage">Percentage (%)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <!-- Input field for discount value -->
                                        <input type="number" class="form-control" step="any" id="discountValue"
                                            name="discountValue" placeholder="Enter Value">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" align="center">
                                        <!-- Hidden input for workshop ID -->
                                        <input type="hidden" name="workshopIdForDiscount" id="workshopIdForDiscount"
                                            data-balance-total="">
                                        <!-- Button to submit the discount -->
                                        <input type="button" id="DiscountForWorkshop" class="btn btn-sm btn-success"
                                            value="Add/Update Discount">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for discount :end -->

        <!-- Email Modal -->
    </section>
    <script src="{{ asset('alerts-boxes/js/sweetalert.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[name="brand"]').select2();
            $('[name="model_number"]').select2();
            $('[name="customer_id"]').select2();


            $('.datepickerForPayment').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                endDate: '+0d',
            });

            // for refresh : start
            $(document).on('click', '.openRefreshBalance', function () {
                var id = $(this).attr('id')

                $.ajax({
                    type: "GET",
                    url: "{{ url('/') }}/AutoCare/sale/edit/" + id,
                    success: function (data) {

                        location.reload();

                    },
                    error: function (data) {
                        swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                    }


                });


            })

            // for refresh : End
            // Open Discount Modal and Set Values
            $(document).on('click', '.openDiscountModelForWorkshop', function () {
                var discountWorkshopId = $(this).attr('id'); // Get the workshop ID
                var balancePrice = $(this).data('balance-total'); // Get the balance price

                // Set the workshop ID and balance price in the modal
                $('[name="workshopIdForDiscount"]').val(discountWorkshopId);
                $('[name="workshopIdForDiscount"]').data('balance-total', balancePrice); // Store balance price in data attribute
                $('[id="discountWorkshopId"]').html(discountWorkshopId); // Display workshop ID in the modal title
            });

            // Handle Discount Submission
            // Handle Discount Submission
            $(document).on('click', '#DiscountForWorkshop', function () {
                const discountType = $('#discountType').val(); // 'amount' or 'percentage'
                const discountValue = parseFloat($('#discountValue').val()); // Discount value entered by the user
                const workshopIdForDiscount = $('[name=workshopIdForDiscount]').val();
                const balancePrice = parseFloat($('[name=workshopIdForDiscount]').data('balance-total'));

                // Validate input
                if (isNaN(discountValue) || discountValue < 0) {
                    swal("Warning!", "Please enter a valid discount value.", "warning");
                    return;
                }

                // Calculate the discount amount based on the type
                let discountAmount = 0;

                if (discountType === 'amount') {
                    discountAmount = discountValue;
                } else if (discountType === 'percentage') {
                    discountAmount = (balancePrice * discountValue) / 100;

                    // Optional: Cap the maximum discount amount
                    const maxDiscountAmount = balancePrice; // Ensure the discount does not exceed the balance price
                    discountAmount = Math.min(discountAmount, maxDiscountAmount);
                }

                // Send the AJAX request with the discount details
                $.ajax({
                    type: "POST",
                    url: "{{ url('/') }}/ajax/discountForWorkshop",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        workshopIdForDiscount: workshopIdForDiscount,
                        discount_type: discountType,
                        discount_value: discountValue,
                        discount_amount: discountAmount,
                    },
                    dataType: 'json',
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            swal("Good job!", "Discount applied successfully", "success");
                            $('#discountValue').val(""); // Clear the input field
                            location.reload();
                        } else {
                            swal("Something went wrong!", response.message || "An error occurred while applying the discount.", "error");
                        }
                    },
                    error: function (xhr, status, error) {
                        swal("Error!", "An unexpected error occurred: " + error, "error");
                    }
                });
            });

            $(document).on('click', '.openDiscountModelForWorkshop', function () {
                const discountWorkshopId = $(this).attr('id'); // Get the workshop ID
                const balancePrice = parseFloat($(this).data('balance-total')); // Get the balance price

                // Fetch the existing discount details via AJAX
                $.ajax({
                    type: "GET",
                    url: `/AutoCare/workshop/search/fetch-discount/${discountWorkshopId}`, // Endpoint to fetch discount details
                    success: function (response) {
                        const { discount_type, discount_value } = response;

                        // Populate the modal fields
                        $('#discountType').val(discount_type || 'amount'); // Default to 'amount' if no discount exists
                        $('#discountValue').val(discount_value || ''); // Pre-fill the discount value

                        // Set hidden fields
                        $('[name="workshopIdForDiscount"]').val(discountWorkshopId);
                        $('[name="workshopIdForDiscount"]').data('balance-total', balancePrice);
                        $('[id="discountWorkshopId"]').html(discountWorkshopId); // Display workshop ID in the modal title
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching discount details:', error);
                    }
                });
            });
            //For Disount : end

            $(document).on('click', '#paymentForWorkshop', function () {
                var creditDebitForWorkshop = $('[name^=creditDebitForWorkshop]').val();
                var workshopIdForPayment = $('[name^=workshopIdForPayment]').val();
                var amountForWorkshop = $('[name^=amountForWorkshop]').val();
                var payment_typeForWorkshop = $('[name^=payment_typeForWorkshop]').val();
                var payment_dateForWorkshop = $('[name^=payment_dateForWorkshop]').val();
                var commentsForWorkshop = $('[name^=commentsForWorkshop]').val();
                const paymentLogId = $('[name="paymentLogId"]').val();

                if (paymentLogId) {
                    // Update existing payment log
                    $.ajax({
                        type: "POST",
                        url: `/AutoCare/workshop/search/update-payment-log/${paymentLogId}`,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            amount: $('[name="amountForWorkshop"]').val(),
                            payment_date: $('[name="payment_dateForWorkshop"]').val(),
                            payment_type: $('[name="payment_typeForWorkshop"]').val(),
                            comments: $('[name="commentsForWorkshop"]').val(),
                        },
                        success: function (response) {
                            if (response.success) {
                                swal("Good job!", "Payment log updated successfully.", "success");
                                location.reload();
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            swal("Error!", "Failed to update payment log.", "error");
                        }
                    });
                } else {
                    if (amountForWorkshop == "") {
                        swal("warning!", "Please enter Amount", "");
                    } else {
                        $.ajax({
                            type: "POST",
                            url: '{{ url('/') }}/ajax/paymentForWorkshop',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                creditDebitForWorkshop: creditDebitForWorkshop,
                                workshopIdForPayment: workshopIdForPayment,
                                amountForWorkshop: amountForWorkshop,
                                payment_typeForWorkshop: payment_typeForWorkshop,
                                payment_dateForWorkshop: payment_dateForWorkshop,
                                commentsForWorkshop: commentsForWorkshop,
                            },
                            dataType: 'html',
                            cache: false,
                            success: function (data) {
                                var workshopIdForPayment = $('[name^=workshopIdForPayment]').val();
                                if (data == 1) {
                                    swal("Good job!", "Workshop Payment  Successfully", "success");
                                    $('[name=amountForWorkshop]').val("");
                                    location.reload();
                                    // var newTab = window.open(
                                    //     "{{ url('/') }}/AutoCare/workshop/view/" +
                                    //     workshopIdForPayment, "_blank");
                                    // newTab.location;
                                    // console.log("Worshop Detail Opened In New Tab");
                                } else if (data == 0) {
                                    swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                                    // $('[name^=amountForWorkshop]').val("");
                                } else {
                                    swal("Somthing Wrong!", data, "error");
                                    // $('[name^=amountForWorkshop]').val("");
                                }


                            },
                            error: function (data) {
                                swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                            }


                        });
                    }
                }

            })

            $(document).on('click', '.openPayentModelForWorkshop', function () {
                var workshopId = $(this).attr('id');
                const balance_price = this.getAttribute('data-grand-total');
                $('[name="workshopIdForPayment"]').val(workshopId)
                $('[id="paymentWorkshopId"]').html(workshopId)
                $('[name="amountForWorkshop"]').val(balance_price)
            })
            $(document).on('click', '.openPayentModelForWorkshop', function () {
                var workshopId = $(this).attr('id');
                const balance_price = this.getAttribute('data-grand-total');

                // Populate hidden fields
                $('[name="workshopIdForPayment"]').val(workshopId);
                $('[id="paymentWorkshopId"]').html(workshopId);
                $('[name="amountForWorkshop"]').val(balance_price);

                // Fetch payment logs
                $.ajax({
                    type: "GET",
                    url: `/AutoCare/workshop/search/get-payment-logs/${workshopId}`,
                    success: function (response) {
                        const paymentLogsTable = $('#paymentLogsTable tbody');
                        paymentLogsTable.empty(); // Clear existing rows

                        response.forEach(log => {
                            const row = `
                                                <tr>
                                                    <td>£${log.debit_amount}</td>
                                                    <td>${log.payment_date}</td>
                                                 <td>${log.payment_type_label}</td>
                                                    <td>${log.comments || '-'}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary editPaymentLog" data-id="${log.id}">Edit</button>
                                                        <button class="btn btn-sm btn-danger deletePaymentLog" data-id="${log.id}">Delete</button>
                                                    </td>
                                                </tr>
                                            `;
                            paymentLogsTable.append(row);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching payment logs:', error);
                    }
                });
            });
            // Edit Payment Log
            $(document).on('click', '.editPaymentLog', function () {
                const paymentLogId = $(this).data('id');
                // Fetch payment log details via AJAX
                $.ajax({
                    type: "GET",
                    url: `/AutoCare/workshop/search/get-payment-log/${paymentLogId}`,
                    success: function (log) {
                        // console.log(log); 
                        // Populate form fields with existing data
                        $('[name="paymentLogId"]').val(paymentLogId);
                        $('[name="amountForWorkshop"]').val(log.debit_amount);
                        $('[name="payment_dateForWorkshop"]').val(log.payment_date);
                        $('[name="payment_typeForWorkshop"]').val(log.payment_type);
                        $('[name="commentsForWorkshop"]').val(log.comments);

                        // Add a hidden field to track the log ID
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'paymentLogId',
                            value: paymentLogId
                        }).appendTo('#formId');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching payment log details:', error);
                    }
                });
            });

            // Submit Updated Payment Log


            // Delete Payment Log
            $(document).on('click', '.deletePaymentLog', function () {
                const paymentLogId = $(this).data('id');

                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this payment log!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: "DELETE",
                            url: `/AutoCare/workshop/search/delete-payment-log/${paymentLogId}`,
                            data: { "_token": "{{ csrf_token() }}" },
                            success: function (response) {
                                if (response.success) {
                                    swal("Good job!", "Payment log deleted successfully.", "success");
                                    location.reload();
                                } else {
                                    swal("Error!", response.message, "error");
                                }
                            },
                            error: function (xhr, status, error) {
                                swal("Error!", "Failed to delete payment log.", "error");
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#closeProdultDetail', function () {
                $('#HideForShowProduct2').hide();
                $('#HideForShowProduct').show();
                $('#productDetail').html("");
            })

            $(document).on('click', '.openPayentModelForProduct', function () {
                var PurchaseId = $(this).attr('id');
                $('[name="PurchaseId"]').val(PurchaseId)
            });

            $(document).on('click', '.openPayentModel', function () {
                var workshopId = $(this).attr('id');
                //  $('[name="PurchaseId"]').val(PurchaseId)

                $.ajax({
                    type: "POST",
                    url: "{{ url('/') }}/ajax/getWorkshopReport",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        workshopId: workshopId
                    },
                    dataType: 'html',
                    cache: false,
                    success: function (data) {
                        $('#HideForShowProduct2').show();
                        $('#HideForShowProduct').hide();
                        workshop_Product = JSON.parse(data);
                        for (index = 0; index < workshop_Product.length; ++index) {
                            $('#productDetail').append("<tr>\
                                            <td>" + workshop_Product[index]['workshop_id'] + "</td>\
                                            <td>" + workshop_Product[index]['workshop_id'] + "</td>\
                                            <td>" + workshop_Product[index]['product_name'] + "</td>\
                                            <td>" + workshop_Product[index]['product_quantity'] + "</td>\
                                            <td>" + workshop_Product[index]['UnitExitPrice'] + "</td>\
                                            <td><a data-toggle=\"modal\" id=\"" + workshop_Product[index]['WorkshopProId'] + "\" data-target=\"#myModal\"  class=\"btn btn-success openPayentModelForProduct btn-sm\"><i class=\"fa fa-undo\" aria-hidden=\"true\"></i></a> </th>\
                                            </tr>");

                            //  thisSelf.parent().parent().find('[name^=model_number]').append(

                            // );  

                        }
                    },
                    error: function (data) {
                        swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                    }


                });


            });


            $(document).ready(function () {

                $(document).on("change", "[name^=brand]", function () {
                    var thisSelf = $(this);
                    var brand = $(this).val();
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/getModal",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            brand: brand,
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            modalData = JSON.parse(data);
                            // console.log(modalData.id);
                            // console.log(modalData.model_name);
                            thisSelf.parent().parent().find('[name^=model_number]')
                                .empty()
                                .append(
                                    '<option selected="selected" value="">-Select -</option>'
                                );
                            for (index = 0; index < modalData.length; ++index) {
                                $('[name^=model_number]').append(
                                    '<option value="' + modalData[index]['id'] +
                                    '">' + modalData[index]['model_name'] +
                                    '</option>'
                                );
                            }
                        }
                    });
                });

                $(document).on("click", "#payment", function () {

                    var quantity = $('[name^=amount]').val();
                    var PurchaseId = $('[name^=PurchaseId]').val();
                    var comments = $('[name^=comments]').val();

                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/submitSaleReturn",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            saleId: PurchaseId,
                            quantity: quantity,
                            comments: comments,
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            if (data == 1) {
                                swal("Good job!",
                                    "Purchase Returned Successfully . Now Quantity Has Been decremented",
                                    "success");
                                $('[name^=PurchaseId]').val("");
                                $('[name^=comments]').val("");
                            } else {
                                swal("Somthing Wrong!", "OOHooooo!!!!!", "error");
                            }
                        },
                        error: function (data) {
                            swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                        }


                    });
                });


            });
        });
    </script>
    <script>
       $(document).on('click', '.open-activity-log-modal', function (e) {
    e.preventDefault();
    let workshopId = $(this).data('id');
    
    // Show loading state
    $('#activity-log-content').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading activity logs...</p></div>');
    $('#activityModal').modal('show');
    
    $.ajax({
        url: `AutoCare/workshop/${workshopId}/activity-log`,
        type: 'GET',
        dataType: 'html',
        timeout: 10000, // 10 seconds timeout
        success: function (response) {
            $('#activity-log-content').html(response);
        },
        error: function (xhr, status, error) {
            console.error('Activity log error:', xhr, status, error);
            let errorMessage = 'Failed to load activity logs.';
            
            if (xhr.status === 404) {
                errorMessage = 'Workshop not found.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred.';
            } else if (status === 'timeout') {
                errorMessage = 'Request timeout. Please try again.';
            }
            
            $('#activity-log-content').html(`<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}</div>`);
        }
    });
});
    </script>
@endsection