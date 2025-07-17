<!DOCTYPE html>
<html>

<head>
    <script src="{{ asset('js/jQuery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('bootstrap-4.1.3/dist/css/bootstrap.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="discription" content="Digital Ideas" />

    <style>
        .grid-container {
            display: grid;
            display: inline-grid;
            grid-template-columns: auto auto auto;
            grid-column-gap: 50px;
        }

        .grid-container2 {
            display: grid;
            display: inline-grid;
            grid-template-columns: 5% 30% 10% 33% 2%;
            grid-column-gap: 4%;
        }

        .grid-container3 {
            display: grid;
            display: inline-grid;
            grid-template-columns: 30% 10% 10% 30%;
            grid-column-gap: 5%;
        }

        .grid-item {
            /*padding: 20px;*/
            font-size: 30px;
            text-align: center;
        }

        p.word-wrap {
            word-break: keep-all;
        }

        .logo {
            border-radius: 15px 50px;
            /*border-radius: 25px;*/
            background-position: left top;
            background-repeat: repeat;
            padding: 15px;
            /* width: 200px;height: 150px; */
            /*background-image: linear-gradient(to bottom right, #867f7f, #c8c8d0);*/
            /*color: skyblue;*/
            color: black;
        }

        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            /*text-align: center;*/
        }

        .badge-red {
            background: red;
            color: #fff;
            font-weight: bold;
            padding: 1px 10px;
            border-radius: 2px;
            text-transform: uppercase;
            font-size: 80%;
        }

        .badge-green {
            background: green;
            color: #fff;
            font-weight: bold;
            padding: 1px 10px;
            border-radius: 2px;
            text-transform: uppercase;
            font-size: 80%;
        }

        .ingray_strip {
            background: rgba(0, 0, 0, 0.05);
            padding: 5px 10px;
            display: flex;
            align-items: center;
            margin: 2em 0;
            border: solid 1px rgba(0, 0, 0, 0.1);
            justify-content: center;
            gap: 5px 40px;
        }

        .ingray_strip .item {}
    </style>
    <title>Estimate Detail</title>
</head>

<body>
    <!-- Container -->
    <div class="container-fluid invoice-container">
       <div class="text-right">
        @if ($estimate->is_converted_to_workshop == 1)
            <a href="{{ url('/') }}/AutoCare/estimate/addinvoice/{{ $estimate->id }}" class="btn btn-warning btn-sm">
                <i class="fa fa-pencil" aria-hidden="true"></i> Update Workshop
            </a>
        @else
            <a href="{{ url('/') }}/AutoCare/estimate/addinvoice/{{ $estimate->id}}" class="btn btn-primary btn-sm">
                <i class="fa fa-wpexplorer" aria-hidden="true"></i> Convert to Workshop
            </a>
        @endif
       </div>
        <!-- Header -->
        <header>
            <div class="row align-items-center gy-3">
                <div class="col-sm-8">
                    <h4 class="text-5 mb-2">{{$garage->garage_name}}</h4>
                    {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}</br>
                    Telephone: {{$garage->mobile}}, Email: {{$garage->email}}<br>
                      @if($garage->vat_number)
                    VAT Number: {{$garage->vat_number}}<br>
                    @endif
                    @if($garage->eori_number)
                        EORI No: {{$garage->eori_number}}<br>
                    @endif
                    @if($garage->company_number)
                  Registration No: {{$garage->company_number}}
                   @endif
                </div>
                <?php
// Get the current domain
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
// Set the path for domain-specific logo if it exists
$domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}");
$themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}");
$defaultLogoPath = public_path("frontend/themes/theme/img/logo/logo.png");
                ?>
                <div class="col-sm-4 text-right">
                    @if(!empty($garage->logo))
                        <img id="logo" src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Garage Name" class="img-bank" /><br>
                    @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                        <img id="logo" src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Garage Name" class="img-bank" /><br>
                    @else
                        <img id="logo" src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}" title="Garage Name"
                            alt="Garage Name" class="img-bank" /><br>
                    @endif
                    <h5 class="mt-2"><strong>Estimate No:</strong> EST-{{ $estimate->id }}</h5>
                    @if($estimateTyre && $estimateTyre->isNotEmpty())
                        @php $tyre = $estimateTyre->first(); @endphp
                        <h5 class="mt-2"><strong>{{ strtoupper(str_replace('_',' ',$tyre->fitting_type)) }}</strong></h5>
                    @endif

                    @if($estimate->payment_status == 1)
                        <span class="badge-green">Paid</span>
                    @elseif($estimate->payment_status == 3)
                        <span class="badge-green">Partial</span>
                    @else
                        <span class="badge-red">Unpaid</span>
                    @endif

                </div>
            </div>
        </header>
@php
if (isset($estimate->workshop_date)) {
    $estimate_date = date('d/m/Y', strtotime($estimate->workshop_date));
} else {
    $estimate_date = "";
}
if (isset($estimate->due_out)) {
    $due_out = date('d/m/Y', strtotime($estimate->due_out));
} else {
    $due_out = "";
}
@endphp
        <!-- Main Content -->
        <main>
            <div class="ingray_strip">
                <div class="item"><strong>Estimate Date:</strong> {{ $estimate_date }}</div>
                <div class="item"> <strong>End Date:</strong> {{ $due_out }}</div>
                <div class="item"> <strong>Est#:</strong> EST-{{ $estimate->id }}</div>
            </div>
            <div class="row">
                <div class="col-sm-6"> <strong>Bill To</strong>
                <address class="mt-2">
                    @if ($estimate->name)
                        <strong>{{ $estimate->name }}</strong><br />
                    @endif
                    @if ($estimate->company_name)
                        Company Name: <strong>{{ $estimate->company_name }}</strong><br />
                    @endif
                    @if ($estimate->reference)
                        Reference: <strong>{{ $estimate->reference }}</strong><br />
                    @endif
                    @if ($estimate->mobile || $estimate->email)
                        Telephone: {{ $estimate->mobile }}<br /> Email: {{ $estimate->email }}<br />
                    @endif
                    @if ( $estimate->address )
                        Addres: {{ $estimate->address }}, {{ $estimate->city }}, {{ $estimate->county }}, {{ $estimate->zone }}, {{ $estimate->country }}
                    @endif
                </address>
                </div>
                <div class="col-sm-6"> 
                    <address class="mt-2 text-sm-end">
                        <h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
                        Registration No: {{$garage->company_number}}<br>
                        VAT Number: {{$garage->vat_number}}<br>
                        Telephone: {{$garage->mobile}}<br>
                        Email: {{$garage->email}}<br>
                        {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}
                    </address>
                </div>
            </div>
        <div class="table-responsive mt-1 mb-4">
            <table class="table border mb-0">
                <thead>
                    <tr class="bg-light">
                        <td colspan="7" align="center">
                            <h4 class="m-0 text-uppercase">Estimate</h4>
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td><strong>Reg. No.</strong></td>
                        <td><strong>Make</strong></td>
                        <td><strong>Model / Year</strong></td>
                        <td><strong>Reg. Date</strong></td>
                        <td><strong>VIN</strong></td>
                        <td><strong>Mileage</strong></td>
                        <td><strong>MOT Expiry Date</strong></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Registration Number -->
                        <td class="text-uppercase">
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_reg_number
    ? $estimateVehicle[0]->vehicle_reg_number
    : ($getIndivisualWorkshopDetail['vehicle_reg_number'] ?? '--') }}
                        </td>
        
                        <!-- Make -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_make
    ? $estimateVehicle[0]->vehicle_make
    : ($getIndivisualWorkshopDetail['vehicle_make'] ?? '--') }}
                        </td>
        
                        <!-- Model / Year -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_model
    ? $estimateVehicle[0]->vehicle_model
    : ($getIndivisualWorkshopDetail['vehicle_model'] ?? '--') }}
                            /
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_year
    ? $estimateVehicle[0]->vehicle_year
    : ($getIndivisualWorkshopDetail['vehicle_first_registered'] ?? '--') }}
                        </td>
        
                        <!-- Registration Date -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_first_registered
    ? \Carbon\Carbon::parse($estimateVehicle[0]->vehicle_first_registered)->format('d/m/Y')
    : '--' }}
                        </td>
        
                        <!-- VIN -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_vin
    ? $estimateVehicle[0]->vehicle_vin
    : ($getIndivisualWorkshopDetail['vehicle_vin'] ?? '--') }}
                        </td>
        
                        <!-- Mileage -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() ? '--' : ($getIndivisualWorkshopDetail['vehicle_mileage'] ?? '--') }}
                        </td>
        
                        <!-- MOT Expiry Date -->
                        <td>
                            {{ $estimateVehicle->isNotEmpty() && $estimateVehicle[0]->vehicle_mot_expiry_date
    ? \Carbon\Carbon::parse($estimateVehicle[0]->vehicle_mot_expiry_date)->format('d/m/Y')
    : '--' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @php
$total_service_price = 0;
$total_product_price = 0;
$total_Tax_Amount = 0;
$grandTotal = 0;
$discount_price = $discount_price ?? 0;
$installmentPayment = $installmentPayment ?? 0;
$paid_price = $paid_price ?? 0;
$itemIndex = 1;
            @endphp

            @if (!empty($estimateTyre) || !empty($estimateService))
            <div class="invoice_item">
                    <table class="table border mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th>#</th>
                                <th>Description</th>
                                <th>Rate</th>
                                <th>VAT %</th>
                                <th>Qty</th>
                                <th>Item Total</th>
                                <th>Amount (Inc. Tax)</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($estimateTyre as $tyre)
                                            @php
        $quantity = $tyre->quantity ?? 1;
        $vatRate = $tyre->tax_class_id == 9 ? 0.2 : 0;
        $vatText = $vatRate > 0 ? 'VAT 20%' : 'VAT 0%';
        $price = $tyre->margin_rate ?? 0;
        $itemTotal = $price * $quantity;
        $vatAmount = $itemTotal * $vatRate;
        $totalAmount = $itemTotal + $vatAmount;
        $total_Tax_Amount += $vatAmount;
        $total_product_price += $itemTotal;
                            @endphp
                                <tr>
                                    <td>{{ $itemIndex++ }}</td>
                                    <td>{{ $tyre->description }} <br>({{ $tyre->tyre_source }}) ({{ $tyre->product_ean }})</td>
                                    <td align="center">£{{ number_format($price, 2) }}</td>
                                    <td>{{ $vatText }}</td>
                                    <td align="center">{{ $quantity }}</td>
                                    <td align="center">£{{ number_format($itemTotal, 2) }}</td>
                                    <td align="right"><strong>£{{ number_format($totalAmount, 2) }}</strong></td>
                                </tr>
                                @endforeach
                                @if( !empty($tyre) && $tyre->fitting_type === 'mobile_fitted')
                                <tr>
                                    <!-- <td>{{ $itemIndex++ }}</td> -->
                                     <td>

                                         <td><strong>Mobile Fitting CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                     </td>
                                        <td colspan="" align="center"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong></td>
                                        <td colspan="" align="center"><strong>{{ $vatText }}</strong></td>
                                        <td colspan="" align="center"><strong>1</strong></td>
                                        <td colspan="" align="center"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong></td>
                                         <td colspan="" align="right"><strong>£{{ number_format($tyre->shipping_price+($tyre->shipping_price*$vatRate), 2) }}</strong></td>
                                </tr>
                                @endif
                            

                            @foreach($estimateService as $service)
                                            @php
        $quantity = $service->service_quantity ?? 1;
        $vatRate = $service->tax_class_id == 9 ? 0.2 : 0;
        $vatText = $vatRate > 0 ? 'VAT 20%' : 'VAT 0%';
        $price = $service->service_price ?? 0;
        $itemTotal = $price * $quantity;
        $vatAmount = $itemTotal * $vatRate;
        $totalAmount = $itemTotal + $vatAmount;
        $total_Tax_Amount += $vatAmount;
        $total_service_price += $itemTotal;
                                @endphp
                                    <tr>
                                        <td>{{ $itemIndex++ }}</td>
                                        <td>{{ $service->service_name }}</td>
                                        <td>£{{ number_format($price, 2) }}</td>
                                        <td>{{ $vatText }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>£{{ number_format($itemTotal, 2) }}</td>
                                        <td align="right"><strong>£{{ number_format($totalAmount, 2) }}</strong></td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @if (!empty($estimateTyre) && isset($tyre))
                                                <div class="table-responsive">
                                                    <table class="table border mb-0">
                                                        <thead>
                                                            <tr class="bg-light">
                                                                <td><strong>Sub Total</strong></td>
                                                                @if($tyre->fitting_type === 'mobile_fitted')
                                                                    <td><strong>CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                                                @endif
                                                                <td><strong>VAT</strong></td>
                                                                <td><strong>Total</strong></td>
                                                                <td><strong>Discount{{ $estimate->formatted_discount }}</strong></td>
                                                                <td><strong>Amount Due</strong></td>
                                                                <td align="right"><strong>Amount Paid</strong></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                @php
                                                                $shippingVatRates = $tyre->shipping_tax_id == 9 ? 0.2 : 0;
                                                                $shippingVatRate = $tyre->shipping_price * $shippingVatRates;
                                                                $shippingVatPrice = $total_Tax_Amount + $shippingVatRate;
                                                                $shippingTotalPrice = $tyre->shipping_price + $shippingVatRate;

                                                                $subTotal = $total_product_price + $total_service_price;
                                                                $grandTotal = $subTotal + $total_Tax_Amount + $shippingTotalPrice;
                                                                //$balancePrice = $grandTotal - ($installmentPayment + $paid_price + $discount_price);
                                                                @endphp
                                                                <td>£{{ number_format($subTotal, 2) }}</td>
                                                                @if($tyre->fitting_type === 'mobile_fitted')
                                                                    <td>£{{ number_format($tyre->shipping_price, 2) }}</td>
                                                                @endif
                                                                <td>£{{ number_format($shippingVatPrice, 2) }}</td>
                                                                <td>£{{ number_format($estimate->grandTotal, 2) }}</td>
                                                                <td>£{{ number_format($estimate->discount_price, 2) }}</td>
                                                                <td><strong>£{{ number_format($estimate->balance_price, 2) }}</strong></td>
                                                                <td align="right"><strong>£{{ number_format($estimate->paid_price, 2) }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
        @else
                                                <div class="table-responsive">
                                                    <table class="table border mb-0">
                                                        <thead>
                                                            <tr class="bg-light">
                                                                <td><strong>Sub Total</strong></td>
                                                                <td><strong>VAT</strong></td>
                                                                <td><strong>Total</strong></td>
                                                                <td><strong>Discount</strong></td>
                                                                <td><strong>Amount Due</strong></td>
                                                                <td align="right"><strong>Amount Paid</strong></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                @php


    $subTotal = $total_product_price + $total_service_price;
    $grandTotal = $subTotal + $total_Tax_Amount;
    $balancePrice = $grandTotal - ($installmentPayment + $paid_price + $discount_price);
                                                                @endphp
                                                                <td>£{{ number_format($subTotal, 2) }}</td>
                                                                <td>£{{ number_format($total_Tax_Amount, 2) }}</td>
                                                                <td>£{{ number_format($grandTotal, 2) }}</td>
                                                                <td>£{{ number_format($estimate->discount_price, 2) }}</td>
                                                                <td><strong>£{{ number_format($balancePrice, 2) }}</strong></td>
                                                                <td align="right"><strong>£{{ number_format($paid_price, 2) }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
        @endif
        </main>
        <!-- Footer -->
        <footer class="text-center mt-4">
            <p><strong>Descripition/Notes:</strong> {{ $estimate->notes }}</p>
            <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()"
                    class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print &
                    Download</a> </div>
        </footer>
    </div>
</body>
<script src="{{ asset('bootstrap-4.1.3/dist/js/bootstrap.js') }}"></script>

<style>
    /* =================================== */
    /*  Basic Style 
/* =================================== */
    body {
        background: #e7e9ed;
        font-size: 16px;
        line-height: 24px;
        font-weight:500;
    }

    img {
        vertical-align: inherit;
    }

    p {
        line-height: 1.9;
    }
    .table,
    .table> :not(caption)>*>* {
        --bs-table-color: #404040;
    }

    .img-bank {
        max-width: 100%;
        height: auto;
    }

    /* Text Size */
    .text-1 {
        font-size: 12px !important;
        font-size: 0.75rem !important;
    }

    .text-4 {
        font-size: 18px !important;
        font-size: 1.125rem !important;
    }

    .text-5 {
        font-size: 21px !important;
        font-size: 1.3125rem !important;
    }

    /* Background Dark */
    .bg-dark {
        background-color: #111418 !important;
    }

    .card-header {
        padding-top: .75rem;
        padding-bottom: .75rem;
    }

    /* Table */
    .table> :not(:last-child)> :last-child>* {
        border-bottom-color: inherit;
    }

    .table:not(.table-sm)> :not(caption)>*>* {
        padding: 0.4rem;
    }

    .table-sm> :not(caption)>*>* {
        padding: 0.3rem;
    }

    .table td.bg-light,
    .table th.bg-light,
    .table tr.bg-light td,
    .table tr.bg-light th {
        background-color: #f8f9fa !important;
    }

    .table td.bg-light-1,
    .table th.bg-light-1,
    .table tr.bg-light-1 td,
    .table tr.bg-light-1 th {
        background-color: #e9ecef !important;
    }

    .table td.bg-light-2,
    .table th.bg-light-2,
    .table tr.bg-light-2 td,
    .table tr.bg-light-2 th {
        background-color: #dee2e6 !important;
    }

    .table td.bg-light-3,
    .table th.bg-light-3,
    .table tr.bg-light-3 td,
    .table tr.bg-light-3 th {
        background-color: #ced4da !important;
    }

    .table td.bg-light-4,
    .table th.bg-light-4,
    .table tr.bg-light-4 td,
    .table tr.bg-light-4 th {
        background-color: #adb5bd !important;
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .table td,
        .table th {
            background-color: transparent !important;
        }

        .table-responsive {
            padding-left: 0.4px;
            padding-right: 0.4px;
            padding-bottom: 0.4px;
        }

        .table td.bg-light,
        .table th.bg-light,
        .table tr.bg-light td {
            background-color: #f8f9fa !important;
        }
    }

    /* =================================== */
    /*  Layouts
/* =================================== */
    .invoice-container {
        margin: 15px auto;
        padding:70px 70px;
        max-width: 1000px;
        background-color: #fff;
        border: 1px solid #ccc;
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        -o-border-radius: 6px;
        border-radius: 6px;
    }

    @media (max-width: 767px) {
        .invoice-container {
            padding: 35px 20px 70px 20px;
            margin-top: 0px;
            border: none;
            border-radius: 0px;
        }
    }

    /* =================================== */
    /*  Extras
/* =================================== */
    .badge-red {
        background: red;
        color: #fff;
        font-weight: bold;
        padding: 1px 10px;
        border-radius: 2px;
        text-transform: uppercase;
        font-size: 80%;
    }

    .badge-green {
        background: green;
        color: #fff;
        font-weight: bold;
        padding: 1px 10px;
        border-radius: 2px;
        text-transform: uppercase;
        font-size: 80%;
    }

    .ingray_strip {
        background: rgba(0, 0, 0, 0.05);
        padding: 5px 10px;
        display: flex;
        align-items: center;
        margin:1em 0;
        border: solid 1px rgba(0, 0, 0, 0.1);
        justify-content: center;
        gap: 5px 40px;
    }

    .bg-gray {
        background: rgba(0, 0, 0, 0.05);
    }

    .terms-conditions {
        margin-top:4em;
        /*font-size: 80%;
        line-height: normal;*/
    }

    .cash-padding {
        padding-top: 2px;
        padding-bottom: 4px;
    }

    @media (min-width: 576px) {
        .text-sm-end {
            text-align: right !important;
        }

        .text-sm-start {
            text-align: left !important;
        }

        .cash {
            text-align: right;
        }
    }
    .btn-primary {
    color: #fff;
    background-color: #111c43;
    border-color: #111c43;
}
.btn-primary:hover{background-color: #111c43;border-color: #111c43;}
.invoice_item{min-height:385px;border:1px solid #dee2e6;}
</style>

</html>