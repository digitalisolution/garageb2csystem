@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <section class="container-fluid">
    <div class="bg-white p-3 mb-3">
    {{ Form::open([
        'url' => 'AutoCare/workshop/search-invoice',
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
            {{ Form::date('created_at_to', isset($created_at_to) ? $created_at_to : old('created_at_to'), [
                'class' => 'form-control',
                'created_at_to' => 'created_at_to',
                'placeholder' => 'created_at_to'
            ]) }}
        </div>

        <!-- To Date -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label>To Date:</label>
            {{ Form::date('created_at_from', isset($created_at_from) ? $created_at_from : old('created_at_from'), [
                'class' => 'form-control',
                'created_at_from' => 'created_at_from',
                'placeholder' => 'created_at_from'
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
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;min-height:500px;">
                        <table id="datable_1" class="table table-hover" style="font-size: 13px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="white-space: nowrap">Workshop Date</th>
                                    <th style="white-space: nowrap">Job Id</th>
                                    <th style="white-space: nowrap" style="white-space: nowrap">Customer Name</th>
                                    <th style="white-space: nowrap">Mobile</th>
                                    <!-- <th style="white-space: nowrap" >Address</th> -->
                                    <th style="white-space: nowrap">Vehicle Reg. No</th>
                                    <!-- <th width="30%">Model Year</th> -->
                                    {{-- <th style="white-space: nowrap">Brand</th> --}}
                                    {{-- <th style="white-space: nowrap">Model </th> --}}
                                    <!-- <th width="30%">VIN</th>
                                                <th width="30%">Reg Number</th>
                                                <th width="30%">Odometer Reading</th>
                                                <th width="30%">Color</th>
                                                <th width="30%">Fuel Type</th>
                                                <th width="30%">Engine Number</th>
                                                <th width="30%">Key Number</th> -->
                                    {{-- <th style="white-space: nowrap">Due In</th> --}}
                                    {{-- <th style="white-space: nowrap">due Out</th> --}}
                                    {{-- <th style="white-space: nowrap">Advance Received</th> --}}
                                    {{-- <th style="white-space: nowrap">Paid Amount </th> --}}
                                    {{-- <th style="white-space: nowrap">Discount</th> --}}
                                    
                                    <th style="white-space: nowrap">Amount Due</th>
                                    <th style="white-space: nowrap">Grand Total</th>
                                    {{-- <th style="white-space: nowrap">Balance</th> --}}
                                    <th style="white-space: nowrap">Payment Status</th>
                                    <th style="white-space: nowrap">Status</th>
                                    <!-- <th width="30%">Advisor</th> -->
                                    {{-- <th style="white-space: nowrap">Notes</th> --}}
                                    {{-- <th style="white-space: nowrap">Created Date</th> --}}
                                    <!-- <th width="30%">Updated Date</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workshop as $key => $value)
                                                            @php
                                                                // Format 'due_in' date
                                                                $due_in = isset($value['due_in']) ? date('d/m/Y', strtotime($value['due_in'])) : '';

                                                                // Format 'created_at' date
                                                                $created_at = isset($value['created_at']) ? date(
                                                                    'd/m/Y H:i:s',
                                                                    strtotime($value['created_at'])
                                                                ) : '';
                                                                // Format 'due_out' date
                                                                $due_out = isset($value['due_out']) ? date('d/m/Y', strtotime($value['due_out'])) : '';

                                                                // Format 'workshop_date' date
                                                                $workshop_date = isset($value['workshop_date']) ? date(
                                                                    'd/m/Y H:i:s',
                                                                    strtotime($value['workshop_date'])
                                                                ) : '';
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $workshop_date }}</td>
                                                                <td>{{ $value['workshop_id'] }}</td>
                                                                <td>{{ $value['name'] }}</td>

                                                                <td>@php
                                                                    if (isset($value['mobile'])) {
                                                                        echo '' . (string) $value['mobile'];
                                                                    }
                                                                @endphp
                                                                </td>

                                                                <td class="text-uppercase">{{ $value['vehicle_reg_number'] }}</td>
                                                                <td>{{ number_format($value['balance_price'], 2, '.', '') }}</td>

                                                                @php

                                                                    $grandTotal += $value['grandTotal'];
                                                                @endphp

                                                                <td>{{ number_format($value['grandTotal'], 2, '.', '') }}</td>
                                                                <td>
                                <span
                                    class="
                            {{ $value['payment_status'] == 1 ? 'Paid' : ($value['payment_status'] == 3 ? 'Partially' : 'Unpaid') }}">
                                    {{ $value['payment_status'] == 1 ? 'Paid' : ($value['payment_status'] == 3 ? 'Partially' : 'Unpaid') }}
                                </span>
                            </td>

                                                                <td><span class="{{ $value['status'] }}">{{ $value['status'] }}</span></td>

                                                                <td style="white-space: nowrap">
                                                                    <!-- @if ($value['is_workshop'] == 1)
                                                                                                                                                                                                                                                <a href="{{ url('/') }}/AutoCare/workshop/add/{{ $value['id'] }}"
                                                                                                                                                                                                                                                    class="btn btn-success btn-sm"><i class="fa fa-wpexplorer"
                                                                                                                                                                                                                                                        aria-hidden="true"></i>Convert to invoice</i></a>
                                                                                                                                                                                                                                            @endif -->

                                                                    <!-- <a data-toggle="modal" id="{{ $value['id'] }}" data-target="#workshopDiscount"
                                                                                                                                                                                class="btn btn-success openDiscountModelForWorkshop btn-sm"><i
                                                                                                                                                                                    class="fa fa-wpexplorer" aria-hidden="true"></i>Discount</i></a> -->

                                                                    <!-- <a data-toggle="modal" id="{{ $value['id'] }}" data-target="#workshopPayment"
                                                                                                                                                                                class="btn btn-success openPayentModelForWorkshop btn-sm"><i
                                                                                                                                                                                    class="fa fa-credit-card" aria-hidden="true"></i></a> -->

                                                                    <a target="blank"
                                                                        href="{{ url('/') }}/AutoCare/workshop/invoice/{{ $value['workshop_id'] }}"
                                                                        class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                                                        <a href="{{ url('/') }}/AutoCare/workshop/add/{{ $value['workshop_id'] }}"
                                                                        class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>


                                                                    <!-- <a target="blank"
                                                                                                                                                                                href="{{ url('/') }}/AutoCare/workshop/payment_history/{{ $value['id'] }}"
                                                                                                                                                                                class="btn btn-danger btn-sm" title="Payment History"><i
                                                                                                                                                                                    class="fa fa-eye"></i></a> -->




                                                                    <!-- @if ($value['is_workshop'] == 1)
                                                                                                                                                                                <a href="{{ url('/') }}/AutoCare/workshop/add/{{ $value['id'] }}"
                                                                                                                                                                                    class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                                                                                                                                                            @else -->

                                                                    <!-- <a data-toggle="modal" id="{{ $value['id'] }}" data-target="#myModal1"
                                                                                                                                                                                    class="btn btn-success openPayentModel btn-sm"><i class="fa fa-undo"
                                                                                                                                                                                        aria-hidden="true"></i></a>
                                                                                                                                                                                <a href="{{ url('/') }}/AutoCare/sale/edit/{{ $value['id'] }}"
                                                                                                                                                                                    class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a> -->
                                                                    <!-- @endif -->

                                                                    <!-- @if ($role_id == 1)
                                                                                                                                                                                <a href="{{ url('/') }}/AutoCare/workshop/trash/{{ $value['id'] }} "
                                                                                                                                                                                    class="btn btn-danger btn-sm"
                                                                                                                                                                                    onclick="return confirm('Are you sure you want to delete this user?');"><i
                                                                                                                                                                                        class="fa fa-remove"></i></a>
                                                                                                                                                                            @endif -->

                                                                </td>
                                                            </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><b>{{ $paid_price }}</b></td>
                                    <td><b>{{ $installmentPayment }}</b></td>
                                    <td><b>{{ $discount_price }}</b></td>
                                    <td><b>{{ $grandTotal }}</b></td>
                                    <td><b>{{ $balance_price }}</b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="col-lg-12 text-center">

                        </div>

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
                                    <th>Action</th>
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


        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary">Sales Return</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        {{-- <form id="formId" action="{{  url('/') }}/ajax/submitSupplierDetail" method="POST"> --}}
                            {{ csrf_field() }}
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Quantity</th>
                                        <th>Discription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <input type="hidden" name="PurchaseId">

                                        <td><input type="number" class="form-control" step="any" name="amount"></td>
                                        <td>{{ Form::textarea('comments', isset($comments) ? $comments : '', [
        'class' =>
            'form-control ',
        'id' => 'comments',
        'style' => 'height: 40px;'
    ]) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><input type="submit" id="payment" class="btn btn-sm btn-success"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>

                            </table>
                            {{--
                        </form> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <!---- Model end --->



        <!-- Modal for payment : start-->
        <div class="modal fade" id="workshopPayment" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary">Customer Payment For Workshop ID:<p id="paymentWorkshopId">
                            </p>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        {{-- <form id="formId" action="{{  url('/') }}/ajax/submitCustomerPaymentDetail" method="POST"> --}}
                            {{ csrf_field() }}
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="display:none">Credit/Debit</th>
                                        <th>Amount</th>
                                        <th style="display: none">Payment Date</th>
                                        <th>Payment Type</th>
                                        <th>Payment Discription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="display:none">
                                            <select name="creditDebitForWorkshop" class="form-control" style="display:none">
                                                <option value="0" selected>Credit</option>
                                                <option value="1">Debit</option>
                                            </select>
                                            <input type="hidden" name="workshopIdForPayment">
                                        </td>
                                        <td><input type="number" class="form-control" step="any" name="amountForWorkshop">
                                        </td>
                                        <td style="display: none"><input type="text"
                                                class="form-control datepickerForPayment" id="payementDate"
                                                name="payment_dateForWorkhop"></td>
                                        <td> {{ Form::select('payment_typeForWorkshop', [
        '1' => 'By Cash',
        '2' => 'By
                                                Internate Banking',
        '3' => 'By Cheque'
    ], isset($payment_type) ? $payment_type :
        '', ['class' => 'form-control form-control ']) }}
                                        </td>
                                        <td>{{ Form::textarea(
        'commentsForWorkshop',
        isset($comments) ? $comments : '',
        ['class' => 'form-control ', 'id' => 'comments', 'style' => 'height: 40px;']
    ) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><input type="button" id="paymentForWorkshop" class="btn btn-sm btn-success"
                                                value="submit"></td>
                                        <td>
                                            {{-- <button type="reset" class="btn btn-sm btn-danger" name=""> <i
                                                    class="fa fa-ban"></i> Reset</button> --}}
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                            {{--
                        </form> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for payment :end -->



        <!-- Modal for discount : start-->
        <div class="modal fade" id="workshopDiscount" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">Customer Discount For Workshop ID:<span
                                id="discountWorkshopId"></span></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>
                    <div class="modal-body">
                        {{-- <form id="formId" action="{{  url('/') }}/ajax/submitCustomerPaymentDetail" method="POST"> --}}
                            {{ csrf_field() }}
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th align="center">Amount</th>
                                        {{-- <th>Discount Discription</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                        <td><input type="number" class="form-control" step="any"
                                                name="amountForWorkshopDiscount">
                                            <input type="hidden" name="workshopIdForDiscount">
                                        </td>
                                        {{-- <td>{{Form::textarea('commentsForWorkshopDiscount', "", ['class' =>
                                            'form-control ',"style"=>"height: 40px;"])}}
                                        </td> --}}
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>

                                        {{-- <td></td> --}}
                                        <td colspan="2" align="center"><input type="button" id="DiscountForWorkshop"
                                                class="btn btn-sm btn-success" value="Add/Update Discount"></td>
                                    </tr>
                                </tfoot>

                            </table>
                            {{--
                        </form> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for discount :end -->


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


            //For Disount : start
            $(document).on('click', '#DiscountForWorkshop', function () {
                var amountForWorkshopDiscount = $('[name=amountForWorkshopDiscount]').val();
                var workshopIdForDiscount = $('[name=workshopIdForDiscount]').val();
                // var commentsForWorkshopDiscount=$('[name=commentsForWorkshopDiscount]').val();


                if (amountForWorkshopDiscount == "") {
                    swal("warning!", "Please enter Amount", "");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/discountForWorkshop",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            amountForWorkshopDiscount: amountForWorkshopDiscount,
                            workshopIdForDiscount: workshopIdForDiscount,
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            var workshopIdForDiscount = $('[name=workshopIdForDiscount]').val();
                            if (data == 1) {
                                swal("Good job!", " Discount  Successfully", "success");
                                $('[name=amountForWorkshopDiscount]').val("");
                                var newTab = window.open(
                                    "{{ url('/') }}/AutoCare/workshop/view/" +
                                    workshopIdForDiscount, "_blank");
                                newTab.location;
                                console.log("Worshop Detail Opened In New Tab");
                            } else {
                                swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                                $('[name^=amountForWorkshop]').val("");
                            }


                        },
                        error: function (data) {
                            swal("Somthing Wrong!", "OOHooooooooooo!!!!", "error");
                        }


                    });
                }

            })

            $(document).on('click', '.openDiscountModelForWorkshop', function () {
                var discountWorkshopId = $(this).attr('id');
                $('[name="workshopIdForDiscount"]').val(discountWorkshopId)
                $('[id="discountWorkshopId"]').html(discountWorkshopId)
            })



            //For Disount : end

            $(document).on('click', '#paymentForWorkshop', function () {
                var creditDebitForWorkshop = $('[name^=creditDebitForWorkshop]').val();
                var workshopIdForPayment = $('[name^=workshopIdForPayment]').val();
                var amountForWorkshop = $('[name^=amountForWorkshop]').val();
                var payment_dateForWorkhop = $('[name^=payment_dateForWorkhop]').val();
                var payment_typeForWorkshop = $('[name^=payment_typeForWorkshop]').val();
                var payment_dateForWorkhop = $('[name^=payment_dateForWorkhop]').val();
                var commentsForWorkshop = $('[name^=commentsForWorkshop]').val();
                if (amountForWorkshop == "") {
                    swal("warning!", "Please enter Amount", "");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/paymentForWorkshop",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            creditDebitForWorkshop: creditDebitForWorkshop,
                            workshopIdForPayment: workshopIdForPayment,
                            amountForWorkshop: amountForWorkshop,
                            payment_dateForWorkhop: payment_dateForWorkhop,
                            payment_typeForWorkshop: payment_typeForWorkshop,
                            payment_dateForWorkhop: payment_dateForWorkhop,
                            commentsForWorkshop: commentsForWorkshop,
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            var workshopIdForPayment = $('[name^=workshopIdForPayment]').val();
                            if (data == 1) {
                                swal("Good job!", "Workshop Payment  Successfully", "success");
                                $('[name=amountForWorkshop]').val("");
                                var newTab = window.open(
                                    "{{ url('/') }}/AutoCare/workshop/view/" +
                                    workshopIdForPayment, "_blank");
                                newTab.location;
                                console.log("Worshop Detail Opened In New Tab");
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

            })

            $(document).on('click', '.openPayentModelForWorkshop', function () {
                var workshopId = $(this).attr('id');
                $('[name="workshopIdForPayment"]').val(workshopId)
                $('[id="paymentWorkshopId"]').html(workshopId)
            })


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
@endsection