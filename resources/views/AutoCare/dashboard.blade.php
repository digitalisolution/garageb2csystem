@extends('samples')
@section('content')
    @php
        $role_id = Auth::user()->role_id;
    @endphp
    <div class="container-fluid">
        <div class="bg-white p-3">
            <div class="animated fadeIn">
                <div class="dashboard_bank">
                    @if ($role_id == 1 || $role_id == 4 )
                        <div class="card text-white bg-dash1">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button type="button" class="btn btn-transparent dropdown-toggle p-0" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('/') }}/AutoCare/workshop/search">Search
                                            Workshop</a>
                                        <a class="dropdown-item" href="{{ url('/') }}/AutoCare/sale/add">Sale Spare</a>
                                        <a class="dropdown-item" href="{{ url('/') }}/AutoCare/sale/sale_return">Return
                                            Spare Log</a>
                                    </div>
                                </div>
                                {{-- <h4 class="mb-0">{{ $totalPendingJob }}</h4> --}}
                                <a href="{{ url('/') }}/AutoCare/workshop/add">
                                    <p style="color:white">Job Sheet</p>s
                                </a>

                            </div>
                            <div class="chart-wrapper px-3 hidden">
                                <canvas id="card-chart1" class="chart" height="70"></canvas>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="card text-white bg-dash2">
                            <div class="card-body pb-0">
                                <button type="button" class="btn btn-transparent p-0 float-right">
                                    <i class="icon-location-pin"></i>
                                </button>
                                <h4 class="mb-0">{{ $totalPendingJob }}</h4>
                                <p>Pending Job</p>
                            </div>
                            <div class="chart-wrapper px-3 hidden">
                                <canvas id="card-chart2" class="chart" height="70"></canvas>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="card text-white bg-dash3">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button type="button" class="btn btn-transparent dropdown-toggle p-0" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('/') }}/AutoCare/workshop/search">Purchase
                                            Search</a>
                                        <a class="dropdown-item" href="{{ url('/') }}/AutoCare/purchase/purhase_return">Purchase
                                            Return</a>
                                        {{-- <a class="dropdown-item" href="{{ url('/') }}/AutoCare/workshop/search">Something
                                            else here</a> --}}
                                    </div>
                                </div>
                                <a href="{{ url('/') }}/AutoCare/workshop/add">
                                    <p style="color:white">Purchase</p>
                                </a>
                            </div>
                            <div class="chart-wrapper hidden">
                                <canvas id="card-chart3" class="chart" height="70"></canvas>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="card text-white bg-dash4">
                            <div class="card-body pb-0">
                                <div class="btn-group float-right">
                                    <button type="button" class="btn btn-transparent dropdown-toggle p-0" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-settings"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ url('/') }}/CustomerCreditDebitLog/add">Customer
                                            Payment Log</a>
                                        {{-- <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a> --}}
                                    </div>
                                </div>
                                <h4 class="mb-0">{{ $TotalCustomers }}</h4>
                                <a href="{{ url('/') }}/AutoCare/customer/add">
                                    <p style="color:white">Customer</p>
                                </a>
                            </div>
                            <div class="chart-wrapper px-3 hidden">
                                <canvas id="card-chart4" class="chart" height="70"></canvas>
                            </div>
                        </div>

                   
                    <!--/.col-->
                </div>
                <!--/.row-->
                <h3 class="cart-page-title">Booking Calendar</h3>
                <div id="calendar"></div>
            </div>

        </div>
    </div>

    <!-- Modal for Booking Details -->
    <!-- Modal for Booking Form -->
    <div class="modal fade" id="bookingFormModal" tabindex="-1" aria-labelledby="bookingFormModalLabel">
        <div class="modal-dialog" style="max-width:90vw">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingFormModalLabel">Create New Booking</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('AutoCare.workshop.add-booking')

                </div>
            </div>
        </div>
    </div>
    @include('AutoCare/workshop/tyre-modal')
    @include('AutoCare/workshop/service-modal')
    <!-- Modal -->
    <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailsModalLabel">Vehicle Details <span class="badge bg-success"
                            id="jobid">JOB ID: </span></h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Booking Information -->
                    <div id="booking_info" class="module-section bg-light p-3 border rounded mb-3">
                        <h6>Vehicle Information</h6>
                        <div class="bg-white border rounded p-2 text-uppercase">

                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div id="customer_info" class="module-section bg-light p-3 border rounded mb-3">
                        <h6>Customer Details</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Customer Phone</th>
                                        <th>Customer Email</th>
                                        <th>Customer Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span id="customerName">N/A</span></td>
                                        <td><span id="customerPhone">N/A</span></td>
                                        <td><span id="customerEmail">N/A</span></td>
                                        <td><span id="customerAddress">N/A</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Booking Date and Time -->
                    <div id="booking_datetime" class="module-section bg-light p-3 border rounded mb-3">
                        <h6>Booking Date and Time</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span id="bookingStartDate"></span></td>
                                        <td><span id="bookingEndDate"></span></td>
                                        <td><span id="bookingStartTime"></span></td>
                                        <td><span id="bookingEndTime"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Workshop Information -->
                    <div id="workshop_info" class="module-section bg-light p-3 border rounded mb-3">
                        <h6>Workshop Information</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Workshop Name</th>
                                        <th>Due In</th>
                                        <th>Due Out</th>
                                        <th>Grand Total</th>
                                        <th>Payment Method</th>
                                        <th>Fitting Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span id="workshopName"></span></td>
                                        <td><span id="workshopDueIn"></span></td>
                                        <td><span id="workshopDueOut"></td>
                                        <td><span id="workshopGrandTotal"></span></td>
                                        <td><span id="workshopPaymentMethod"></span></td>
                                        <td><span id="fittingType"></span></td>
                                        <td><span id="workshopStatus"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    <div id="workshop_items_services" class="module-section bg-light p-3 border rounded mb-3">
                        <div class="information_bank1">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><strong>#</strong></th>
                                        <th><strong>Item</strong></th>
                                        <th class="text-center"><strong>Qty</strong></th>
                                        <th class="text-right"><strong>Rate</strong></th>
                                        <th class="text-right"><strong>VAT</strong></th>
                                        <th class="text-right"><strong>Amount</strong></th>
                                    </tr>
                                </thead>
                                <tbody id="workshopItems">
                                    <!-- Data will be populated here dynamically -->
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <!-- <thead class="bg-light">
                                                            <tr>
                                                                <th></th>
                                                                <th class="text-right"></th>
                                                                <th class="text-right"></th>
                                                            </tr>
                                                        </thead> -->
                                <tbody>
                                    <tr>
                                        <th class="text-right"><span id="subtotal"></span></th>
                                    </tr>
                                    <tr id="calloutchargetr">
                                        <th class="text-right"><span id="calloutcharge"></span></th>
                                    </tr>
                                    <tr>
                                        <th class="text-right"><span id="totalvat"></span></th>
                                    </tr>

                                    <tr>
                                        <th class="text-right"><span id="grandtotal"></span></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="editBookingBtn" href="" class="btn btn-primary">Edit</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-12">
        <div class="card text-white bg-info">
            <div class="card-body pb-0">
                <div>Welcome</div>
            </div>
            <div class="chart-wrapper px-3 hidden">
                <canvas id="card-chart4" class="chart" height="70"></canvas>
            </div>
        </div>
    </div>
@endif
    <style type="text/css">
        .fc-view-harness-active {
            height: 580.519px !important;
        }

        .fc .fc-bg-event {
            color: #fff;
        }

        .fc-timegrid-event-short .fc-event-time::after {
            content: "" !important;
        }

        .fc-event-time {
            margin-right: 5px;
        }
        .fc-timegrid-event-harness-inset .fc-timegrid-event, .fc-timegrid-event.fc-event-mirror, .fc-timegrid-more-link{
        max-width: 150px !important;
        max-height: 50px !important;
        }

        #booking_info .bg-white span {
            margin: 4px;
        }
    </style>
@endsection
<!-- /.conainer-fluid -->