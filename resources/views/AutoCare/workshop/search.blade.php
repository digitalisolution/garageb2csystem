@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <section class="container-fluid">
        <div class="bg-white p-3 mb-3">
            <!-- Toggle Button for Filters -->
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filters</h5> <!-- Optional: Add a title -->
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
                    data-bs-target="#workshopFilters" aria-expanded="true" aria-controls="workshopFilters"
                    id="filterToggleBtn">
                    <!-- Icons will be toggled by JS -->
                    <span id="filterToggleText">Show Filters</span>
                    <i id="filterToggleIcon" class="fa fa-chevron-up ms-1"></i> <!-- Font Awesome Icon -->
                </button>
            </div>

            <!-- Collapsible Filter Form -->
            <!-- Show initially by adding 'show' class. Remove 'show' if you want it hidden by default -->
            <div class="collapse" id="workshopFilters">
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
                        {{ Form::text('id', request('id', old('id')), [ // Use request() helper for cleaner syntax
        'class' => 'form-control',
        'id' => 'id',
        'placeholder' => 'Job Id'
    ]) }}
                    </div>

                    <!-- Customer Name -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Customer Name:</label>
                        {{ Form::text('name', request('name', old('name')), [
        'class' => 'form-control',
        'name' => 'name', // This attribute is redundant if using Form::text
        'placeholder' => 'Name'
    ]) }}
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Mobile Number:</label>
                        {{ Form::text('mobile', request('mobile', old('mobile')), [ // Fixed variable name typo
        'class' => 'form-control',
        'placeholder' => 'Mobile' // Removed incorrect attribute name
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
                        <label for="origin">Origin:</label>
                        {{ Form::select('origin', ['website' => 'Website', 'admin' => 'Admin'], request('origin', old('origin')), [
        'id' => 'origin',
        'class' => 'form-control',
        'placeholder' => 'Select Origin'
    ]) }}
                    </div>

                    <!-- Invoice -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="convert_to_invoice">Convert Invoice:</label>
                        {{ Form::select('convert_to_invoice', ['1' => 'Invoice', '0' => 'Workshop'], request('convert_to_invoice', old('convert_to_invoice')), [
        'id' => 'convert_to_invoice',
        'class' => 'form-control',
        'placeholder' => 'Select Invoice Status'
    ]) }}
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="status">Workshop Status:</label>
                        {{ Form::select('status', ['booked' => 'Booked', 'completed' => 'Completed', 'failed' => 'Failed', 'pending' => 'Pending'], request('status', old('status')), [
        'id' => 'status',
        'class' => 'form-control',
        'placeholder' => 'Select Workshop Status'
    ]) }}
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="payment_method">Payment Method:</label>
                        {{ Form::select('payment_method', ['pay_at_fitting_center' => 'Pay at Center', 'dojo' => 'dojo', 'global_payment' => 'Global Pay'], request('payment_method', old('payment_method')), [
        'id' => 'payment_method',
        'class' => 'form-control',
        'placeholder' => 'Select Payment Method'
    ]) }}
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="is_void">Void Invoices:</label>
                        {{ Form::select('is_void', ['1' => 'Yes', '0' => 'No'], request('is_void', old('is_void')), [
        'id' => 'is_void',
        'class' => 'form-control',
        'placeholder' => 'Search Void Invoices'
    ]) }}
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="payment_status">Payment Status:</label>
                        {{ Form::select('payment_status', ['1' => 'Paid', '0' => 'Unpaid', '3' => 'Partial'], request('payment_status', old('payment_status')), [
        'id' => 'payment_status',
        'class' => 'form-control',
        'placeholder' => 'Select payment status'
    ]) }}
                    </div>

                    <!-- Vehicle Registration Number -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label>Vehicle Registration Number:</label>
                        {{ Form::text('vehicle_reg_number_for_search', request('vehicle_reg_number_for_search', old('vehicle_reg_number_for_search')), [
        'class' => 'form-control',
        'placeholder' => 'Vehicle Reg No.' // Removed incorrect attribute name
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
                        <i class="fa fa-align-justify"></i> Workshop Detail
                        <a class="btn btn-primary text-center float-right"
                            href="{{ asset('/AutoCare/workshop/add') }}">Create New Workshop</a>


                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;">
                        <table id="workshopTable" class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="white-space: nowrap">Job Date</th>
                                    <th style="white-space: nowrap">Job Id</th>
                                    <th style="white-space: nowrap">Name</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <th style="white-space: nowrap">Reg. No</th>
                                    <th style="white-space: nowrap">Pymt Method</th>
                                    <th style="white-space: nowrap">Amnt Due</th>
                                    <th style="white-space: nowrap">Total</th>
                                    <th style="white-space: nowrap">Payment</th>
                                    <th style="white-space: nowrap">Origin</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <th style="white-space: nowrap">Invoice</th>
                                    <th align="right">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th colspan="6"></th>
                                </tr>
                            </tfoot>
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
        <!-- Email Modal -->
        <!-- Reusable Email Modal -->
        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="emailForm" action="{{ url('/AutoCare/workshop/send-invoice-email') }}" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" id="invoice_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="emailModalLabel">Send Invoice via Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-left">
                            <div class="form-group mb-2">
                                <label for="email_to">Email To</label>
                                <input type="email" class="form-control" id="email_to" name="email_to" required>
                            </div>

                            <div class="form-group mb-2">
                                <label for="email_cc">CC</label>
                                <input type="email" class="form-control" id="email_cc" name="email_cc">
                            </div>

                            <div class="form-group mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" checked id="attach_pdf"
                                        name="attach_pdf" value="1">
                                    <label class="form-check-label p-0" for="attach_pdf">Attach Invoice PDF</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email_body">Body</label>
                                <textarea id="email_body" name="email_body"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Email</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <style>
            .tox-statusbar__branding,
            .tox-promotion {
                display: none
            }
        </style>


        <style>
            .tox-statusbar__branding,
            .tox-promotion {
                display: none;
            }
        </style>
        <!-- Email Modal end -->
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
                            $('#discountValue').val(""); location.reload();
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
                const discountWorkshopId = $(this).attr('id');
                const balancePrice = parseFloat($(this).data('balance-total'));
                // Fetch the existing discount details via AJAX
                $.ajax({
                    type: "GET",
                    url: `/AutoCare/workshop/search/fetch-discount/${discountWorkshopId}`,
                    success: function (response) {
                        const { discount_type, discount_value } = response;

                        // Populate the modal fields
                        $('#discountType').val(discount_type || 'amount');
                        $('#discountValue').val(discount_value || '');

                        // Set hidden fields
                        $('[name="workshopIdForDiscount"]').val(discountWorkshopId);
                        $('[name="workshopIdForDiscount"]').data('balance-total', balancePrice);
                        $('[id="discountWorkshopId"]').html(discountWorkshopId);
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
    <!-- JavaScript to toggle icon and text -->
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
            var table = $('#workshopTable').DataTable({
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
                    url: "{{ route('workshop.data') }}",
                    data: function (d) {
                        d.id = $('input[name="id"]').val();
                        d.name = $('input[name="name"]').val();
                        d.mobile = $('input[name="mobile"]').val();
                        d.created_at_from = $('input[name="created_at_from"]').val();
                        d.created_at_to = $('input[name="created_at_to"]').val();
                        d.email = $('input[name="email"]').val();
                        d.origin = $('select[name="origin"]').val();
                        d.convert_to_invoice = $('select[name="convert_to_invoice"]').val();
                        d.status = $('select[name="status"]').val();
                        d.payment_method = $('select[name="payment_method"]').val();
                        d.is_void = $('select[name="is_void"]').val();
                        d.payment_status = $('select[name="payment_status"]').val();
                        d.vehicle_reg_number_for_search = $('input[name="vehicle_reg_number_for_search"]').val();
                    }
                },
                columns: [
                    { data: 'workshop_date_formatted', name: 'workshops.created_at' },
                    { data: 'id', name: 'workshops.id' },
                    { data: 'customer_name', name: 'workshops.name' },
                    { data: 'mobile', name: 'workshops.mobile' },
                    { data: 'vehicle_reg', name: 'workshops.vehicle_reg_number' },
                    { data: 'payment_method_formatted', name: 'workshops.payment_method' },
                    { data: 'amount_due', name: 'workshops.balance_price' },
                    { data: 'grand_total', name: 'workshops.grandTotal' },
                    { data: 'payment_status_badge', name: null, orderable: true, searchable: true },
                    { data: 'origin_badge', name: 'workshops.workshop_origin' },
                    { data: 'status_badge', name: 'workshops.status' },
                    { data: 'invoice_convert_badge', name: null, orderable: true, searchable: true },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],

                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\£,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    totalDue = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    totalGrand = api
                        .column(7)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    $(api.column(6).footer()).html(
                        '£' + parseFloat(totalDue).toFixed(2)
                    );
                    $(api.column(7).footer()).html(
                        '£' + parseFloat(totalGrand).toFixed(2)
                    );
                }

            });
        });
    </script>
    <script>
        $(document).on('click', '.open-email-modal-btn', function () {
            const invoiceId = $(this).data('workshop-id');
            const email = $(this).data('workshop-email') || '';
            const b64 = $(this).attr('data-email-body-b64') || '';
            let emailBody = '';

            try {
                emailBody = atob(b64);
            } catch (e) {
                emailBody = '';
            }

            $('#invoice_id').val(invoiceId);
            $('#email_to').val(email);

            if (typeof tinymce !== 'undefined' && tinymce.get('email_body')) {
                tinymce.get('email_body').setContent(emailBody);
            } else {
                $('#email_body').val(emailBody);
            }

            $('#emailModal').modal('show');
        });

        if (typeof tinymce !== 'undefined') {
            $('#emailForm').on('submit', function () {
                const ed = tinymce.get('email_body');
                if (ed) {
                    $('#email_body').val(ed.getContent());
                }
            });
        }
    </script>

@endsection