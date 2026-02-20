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
    <title>Workshop Detail</title>
</head>

<body>
    <!-- Container -->
    <div class="container-fluid invoice-container">
        <!-- Header -->
        <header>
            <div class="row align-items-center gy-3">
                <div class="col-sm-8">
                    <h4 class="text-5 mb-2">{{$garage->garage_name}}</h4>
                    {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}</br>
                    Telephone: {{$garage->mobile}}, Email: {{$garage->email}}<br>
                    VAT Number: {{$garage->vat_number}}<br>
                    Registration No: {{$garage->company_number}}
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

                    <h5 class="mt-2"><strong>Job No:</strong> JOB-{{ $workshop->id }}</h5>
                    @if($WorkshopTyre)
                        @foreach($WorkshopTyre as $tyre)
                        @endforeach
                        <h5 class="mt-2"><strong></strong>{{ strtoupper(str_replace('_', ' ', $tyre->fitting_type)) }}</h5>
                    @endif
                    @if($workshop->payment_status == 1)
                        <span class="badge-green">Paid</span>
                    @elseif($workshop->payment_status == 0)
                        <span class="badge-red">Unpaid</span>
                    @elseif($workshop->payment_status == 3)
                        <span class="badge-red">Partially</span>
                    @endif


                </div>
            </div>
        </header>
        @php
            if (isset($workshop->workshop_date)) {
                $workshop_date = strtotime($workshop->workshop_date);
                $workshop_date = date('d/m/Y', $workshop_date);
            } else {
                $workshop_date = "";
            }
            if (isset($workshop->due_out)) {
                $due_out = strtotime($workshop->due_out);
                $due_out = date('d/m/Y', $due_out);
            } else {
                $due_out = "";
            }
        @endphp
        <!-- Main Content -->
        <main>
            <div class="ingray_strip">
                <div class="item"><strong>Job Date:</strong> {{ $workshop->workshop_date }}</div>
                <div class="item"> <strong>Due Date:</strong> {{ $workshop->due_out }}</div>
                <div class="item"> <strong>Job#:</strong> JOB-{{ $workshop->id }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <strong>Garage Address:</strong>
                    <address class="mt-2">
                        <h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
                        {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}</br>
                        Telephone: {{$garage->mobile}}, Email: {{$garage->email}}<br>
                        VAT Number: {{$garage->vat_number}}<br>
                        Registration No: {{$garage->company_number}}
                    </address>
                </div>
                <div class="col-sm-6"> <strong>Delivery Address:</strong>
                    <h4 class="text-4 mb-1">{{$workshop->name}} {{$workshop->last_name}}</h4>
                    <address>
                        {{ $workshop->address }}, {{ $workshop->city }}, {{ $workshop->county }}, {{ $workshop->zone }}
                        <br />
                        Telephone: {{ $workshop->mobile }}<br /> Email: {{ $workshop->email }}
                    </address>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border mb-0">
                    <thead>
                        <tr class="bg-light">
                            <td colspan="7" align="center">
                                <h4 class="m-0 text-uppercase">JOB</h4>
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
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_reg_number
    ? $WorkshopVehicle[0]->vehicle_reg_number
    : ($getIndivisualWorkshopDetail['vehicle_reg_number'] ?? '--') }}
                            </td>

                            <!-- Make -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_make
    ? $WorkshopVehicle[0]->vehicle_make
    : ($getIndivisualWorkshopDetail['vehicle_make'] ?? '--') }}
                            </td>

                            <!-- Model / Year -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_model
    ? $WorkshopVehicle[0]->vehicle_model
    : ($getIndivisualWorkshopDetail['vehicle_model'] ?? '--') }}
                                /
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_year
    ? $WorkshopVehicle[0]->vehicle_year
    : ($getIndivisualWorkshopDetail['vehicle_first_registered'] ?? '--') }}
                            </td>

                            <!-- Registration Date -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_first_registered
    ? \Carbon\Carbon::parse($WorkshopVehicle[0]->vehicle_first_registered)->format('d/m/Y')
    : '--' }}
                            </td>

                            <!-- VIN -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_vin
    ? $WorkshopVehicle[0]->vehicle_vin
    : ($getIndivisualWorkshopDetail['vehicle_vin'] ?? '--') }}
                            </td>

                            <!-- Mileage -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() ? '--' : ($getIndivisualWorkshopDetail['vehicle_mileage'] ?? '--') }}
                            </td>

                            <!-- MOT Expiry Date -->
                            <td>
                                {{ $WorkshopVehicle->isNotEmpty() && $WorkshopVehicle[0]->vehicle_mot_expiry_date
    ? \Carbon\Carbon::parse($WorkshopVehicle[0]->vehicle_mot_expiry_date)->format('d/m/Y')
    : '--' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <br />

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

            @if (!empty($WorkshopTyre) || !empty($WorkshopService))
                <table class="table border mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th>#</th>
                            <th>Item</th>
                            <th>Rate</th>
                            <th>VAT %</th>
                            <th>Qty</th>
                            <th>Item Total</th>
                            <th>Amount (Inc. Tax)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($WorkshopTyre as $tyre)
                            @php
                                $quantity = $tyre->quantity ?? 1;
                                $vatRate = $tyre->tax_class_id == 9 ? 0.2 : 0;
                                $vatText = $vatRate > 0 ? 'VAT 20%' : 'VAT 0%';
                                $garageVatClass = $tyre->garage_vat_class == 9 ? 0.2 : 0;
                                $garageVatText = $garageVatClass > 0 ? 'VAT 20%' : 'VAT 0%';
                                $garageFittingCharges = $tyre->garage_fitting_charges;
                                $price = $tyre->margin_rate ?? 0;
                                $itemTotal = $price * $quantity;
                                $vatAmount = $itemTotal * $vatRate;
                                $totalAmount = $itemTotal + $vatAmount;
                                $total_Tax_Amount += $vatAmount;
                                $total_product_price += $itemTotal;
                            @endphp
                            <tr>
                                <td>{{ $itemIndex++ }}</td>
                                <td>{{ $tyre->description }} <br>({{ $tyre->product_ean }})</td>
                                <td align="center">£{{ number_format($price, 2) }}</td>
                                <td>{{ $vatText }}</td>
                                <td align="center">{{ $quantity }}</td>
                                <td align="center">£{{ number_format($itemTotal, 2) }}</td>
                                <td align="right"><strong>£{{ number_format($totalAmount, 2) }}</strong></td>
                            </tr>
                        @endforeach
                        @if(!empty($tyre) && in_array($tyre->fitting_type, ['mobile_fitted', 'mailorder']))
                            <tr>
                                <!-- <td>{{ $itemIndex++ }}</td> -->
                                <td>
                                <td><strong>{{ str_replace('_', ' ', ucfirst($tyre->fitting_type)) }} CallOut
                                        Charge({{$tyre->shipping_postcode}})</strong></td>
                                </td>
                                <td colspan="" align="center"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong>
                                </td>
                                <td colspan="" align="center"><strong>{{ $vatText }}</strong></td>
                                <td colspan="" align="center"><strong>1</strong></td>
                                <td colspan="" align="center"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong>
                                </td>
                                <td colspan="" align="right">
                                    <strong>£{{ number_format($tyre->shipping_price + ($tyre->shipping_price * $vatRate), 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        @if(!empty($tyre) && in_array($tyre->fitting_type, ['fully_fitted']))
                            <tr>
                                <!-- <td>{{ $itemIndex++ }}</td> -->
                                <td>
                                <td><strong>Garage Fitting Charge</strong></td>
                                </td>
                                <td align="center">
                                    <strong>£{{ number_format($quantity > 0 ? $garageFittingCharges / $quantity : 0, 2) }}</strong>
                                </td>
                                <td colspan="" align="center"><strong>{{ $garageVatText }}</strong></td>
                                <td colspan="" align="center"><strong>{{ $quantity }}</strong></td>
                                <td colspan="" align="center"><strong>£{{ number_format($garageFittingCharges, 2) }}</strong>
                                </td>
                                <td colspan="" align="right">
                                    <strong>£{{ number_format($garageFittingCharges + ($garageFittingCharges * $garageVatClass), 2) }}</strong>
                                </td>
                            </tr>
                        @endif


                        @foreach($WorkshopService as $service)
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
            @endif

            @if (!empty($WorkshopTyre) && isset($tyre))
                <div class="table-responsive">
                    <table class="table border mb-0">
                        <thead>
                            <tr>
                                <td><strong>Sub Total</strong></td>
                                @if(!empty($tyre) && in_array($tyre->fitting_type, ['mobile_fitted', 'mailorder']))
                                    <td><strong>CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                @endif
                                @if(!empty($tyre) && in_array($tyre->fitting_type, ['fully_fitted']))
                                    <td><strong>Garage Fitting Charge</strong></td>
                                @endif
                                <td><strong>VAT</strong></td>
                                <td><strong>Total</strong></td>
                                <td><strong>Discount{{ $workshop->formatted_discount }}</strong></td>
                                <td><strong>Amount Due</strong></td>
                                <td align="right"><strong>Amount Paid</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @php
                                    $shippingVatRates = $tyre->shipping_tax_id == 9 ? 0.2 : 0;
                                    $shippingVatRate = $tyre->shipping_price * $shippingVatRates;
                                    $garageVatRates = $tyre->garage_vat_class == 9 ? 0.2 : 0;
                                    $garageFittingVat = $garageFittingCharges * $garageVatRates;
                                    $shippingVatPrice = $total_Tax_Amount + $shippingVatRate + $garageFittingVat;
                                    $shippingTotalPrice = $tyre->shipping_price + $shippingVatRate + $garageFittingVat;

                                    $subTotal = $total_product_price + $total_service_price;
                                    $grandTotal = $subTotal + $total_Tax_Amount + $shippingTotalPrice;
                                    //$balancePrice = $grandTotal - ($installmentPayment + $paid_price + $discount_price);
                                @endphp
                                <td>£{{ number_format($subTotal, 2) }}</td>
                                @if(!empty($tyre) && in_array($tyre->fitting_type, ['mobile_fitted', 'mailorder']))
                                    <td>£{{ number_format($tyre->shipping_price, 2) }}</td>
                                @endif
                                @if(!empty($tyre) && in_array($tyre->fitting_type, ['fully_fitted']))
                                    <td>£{{ number_format($garageFittingCharges, 2) }}</td>
                                @endif
                                <td>£{{ number_format($shippingVatPrice, 2) }}</td>
                                <td>£{{ number_format($workshop->grandTotal, 2) }}</td>
                                <td>£{{ number_format($workshop->discount_price, 2) }}</td>
                                <td><strong>£{{ number_format($workshop->balance_price, 2) }}</strong></td>
                                <td align="right"><strong>£{{ number_format($workshop->paid_price, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else

                <div class="table-responsive">
                    <table class="table border mb-0">
                        <thead>
                            <tr>
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
                                <td>£{{ number_format($discount_price, 2) }}</td>
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
            <p class="text-1"><strong>Descripition/Notes:</strong> {{ $workshop->notes }}</p>
            <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()"
                    class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print &
                    Download</a> </div>
        </footer>
    </div>
</body>
<script src="{{ asset('bootstrap-4.1.3/dist/js/bootstrap.js') }}"></script>
<style>
    /*-----------------------------------------------------------------------------------
=================================================
Table of Contents
=================================================
- Basic
- Helpers Classes
- Layouts
- Extra
-------------------------------------------------------------------*/
    :root,
    [data-bs-theme="light"] {
        --bs-themecolor: #0071cc;
        --bs-themecolor-rgb: 0, 113, 204;
        --bs-themehovercolor: #005599;
        --bs-themehovercolor-rgb: 0, 84.75, 153;
        --bs-link-color: var(--bs-themecolor);
        --bs-link-color-rgb: var(--bs-themecolor-rgb);
        --bs-link-hover-color: var(--bs-themehovercolor);
        --bs-link-hover-color-rgb: var(--bs-themehovercolor-rgb);
        --bs-primary: var(--bs-themecolor);
        --bs-primary-rgb: var(--bs-themecolor-rgb);
        --bs-primary-text-emphasis: #002d52;
        --bs-primary-bg-subtle: #cce3f5;
        --bs-primary-border-subtle: #99c6eb;
        --bs-body-color: #404040;
        --bs-body-color-rgb: 64, 64, 64;
        --bs-heading-color: var(--bs-emphasis-color);
        --bs-body-font-family: Poppins, sans-serif;
    }

    /* =================================== */
    /*  Basic Style 
/* =================================== */
    body {
        background: #e7e9ed;
        font-size: 14px;
        line-height: 22px;
    }

    form {
        padding: 0;
        margin: 0;
        display: inline;
    }

    img {
        vertical-align: inherit;
    }

    p {
        line-height: 1.9;
    }

    iframe {
        border: 0 !important;
    }

    .table,
    .table> :not(caption)>*>* {
        --bs-table-color: #404040;
    }

    .img-bank {
        max-width: 100%;
        height: auto;
    }

    /* =================================== */
    /*  Helpers Classes
/* =================================== */
    /* Border Radius */
    .rounded-top-0 {
        border-top-left-radius: 0px !important;
        border-top-right-radius: 0px !important;
    }

    .rounded-bottom-0 {
        border-bottom-left-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }

    .rounded-left-0 {
        border-top-left-radius: 0px !important;
        border-bottom-left-radius: 0px !important;
    }

    .rounded-right-0 {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }

    /* Text Size */
    .text-0 {
        font-size: 11px !important;
        font-size: 0.6875rem !important;
    }

    .text-1 {
        font-size: 12px !important;
        font-size: 0.75rem !important;
    }

    .text-2 {
        font-size: 14px !important;
        font-size: 0.875rem !important;
    }

    .text-3 {
        font-size: 16px !important;
        font-size: 1rem !important;
    }

    .text-4 {
        font-size: 18px !important;
        font-size: 1.125rem !important;
    }

    .text-5 {
        font-size: 21px !important;
        font-size: 1.3125rem !important;
    }

    .text-6 {
        font-size: 24px !important;
        font-size: 1.50rem !important;
    }

    .text-7 {
        font-size: 28px !important;
        font-size: 1.75rem !important;
    }

    .text-8 {
        font-size: 32px !important;
        font-size: 2rem !important;
    }

    .text-9 {
        font-size: 36px !important;
        font-size: 2.25rem !important;
    }

    .text-10 {
        font-size: 40px !important;
        font-size: 2.50rem !important;
    }

    .text-11 {
        font-size: calc(1.4rem + 1.8vw) !important;
    }

    @media (min-width: 1200px) {
        .text-11 {
            font-size: 2.75rem !important;
        }
    }

    .text-12 {
        font-size: calc(1.425rem + 2.1vw) !important;
    }

    @media (min-width: 1200px) {
        .text-12 {
            font-size: 3rem !important;
        }
    }

    .text-13 {
        font-size: calc(1.45rem + 2.4vw) !important;
    }

    @media (min-width: 1200px) {
        .text-13 {
            font-size: 3.25rem !important;
        }
    }

    .text-14 {
        font-size: calc(1.475rem + 2.7vw) !important;
    }

    @media (min-width: 1200px) {
        .text-14 {
            font-size: 3.5rem !important;
        }
    }

    .text-15 {
        font-size: calc(1.5rem + 3vw) !important;
    }

    @media (min-width: 1200px) {
        .text-15 {
            font-size: 3.75rem !important;
        }
    }

    .text-16 {
        font-size: calc(1.525rem + 3.3vw) !important;
    }

    @media (min-width: 1200px) {
        .text-16 {
            font-size: 4rem !important;
        }
    }

    .text-17 {
        font-size: calc(1.575rem + 3.9vw) !important;
    }

    @media (min-width: 1200px) {
        .text-17 {
            font-size: 4.5rem !important;
        }
    }

    .text-18 {
        font-size: calc(1.625rem + 4.5vw) !important;
    }

    @media (min-width: 1200px) {
        .text-18 {
            font-size: 5rem !important;
        }
    }

    .text-19 {
        font-size: calc(1.65rem + 4.8vw) !important;
    }

    @media (min-width: 1200px) {
        .text-19 {
            font-size: 5.25rem !important;
        }
    }

    .text-20 {
        font-size: calc(1.7rem + 5.4vw) !important;
    }

    @media (min-width: 1200px) {
        .text-20 {
            font-size: 5.75rem !important;
        }
    }

    .text-21 {
        font-size: calc(1.775rem + 6.3vw) !important;
    }

    @media (min-width: 1200px) {
        .text-21 {
            font-size: 6.5rem !important;
        }
    }

    .text-22 {
        font-size: calc(1.825rem + 6.9vw) !important;
    }

    @media (min-width: 1200px) {
        .text-22 {
            font-size: 7rem !important;
        }
    }

    .text-23 {
        font-size: calc(1.9rem + 7.8vw) !important;
    }

    @media (min-width: 1200px) {
        .text-23 {
            font-size: 7.75rem !important;
        }
    }

    .text-24 {
        font-size: calc(1.95rem + 8.4vw) !important;
    }

    @media (min-width: 1200px) {
        .text-24 {
            font-size: 8.25rem !important;
        }
    }

    .text-25 {
        font-size: calc(2.025rem + 9.3vw) !important;
    }

    @media (min-width: 1200px) {
        .text-25 {
            font-size: 9rem !important;
        }
    }

    /* Line height */
    .line-height-07 {
        line-height: 0.7 !important;
    }

    .line-height-1 {
        line-height: 1 !important;
    }

    .line-height-2 {
        line-height: 1.2 !important;
    }

    .line-height-3 {
        line-height: 1.4 !important;
    }

    .line-height-4 {
        line-height: 1.6 !important;
    }

    .line-height-5 {
        line-height: 1.8 !important;
    }

    /* Font Weight */
    .fw-100 {
        font-weight: 100 !important;
    }

    .fw-200 {
        font-weight: 200 !important;
    }

    .fw-300 {
        font-weight: 300 !important;
    }

    .fw-400 {
        font-weight: 400 !important;
    }

    .fw-500 {
        font-weight: 500 !important;
    }

    .fw-600 {
        font-weight: 600 !important;
    }

    .fw-700 {
        font-weight: 700 !important;
    }

    .fw-800 {
        font-weight: 800 !important;
    }

    .fw-900 {
        font-weight: 900 !important;
    }

    /* Opacity */
    .opacity-0 {
        opacity: 0;
    }

    .opacity-1 {
        opacity: 0.1;
    }

    .opacity-2 {
        opacity: 0.2;
    }

    .opacity-3 {
        opacity: 0.3;
    }

    .opacity-4 {
        opacity: 0.4;
    }

    .opacity-5 {
        opacity: 0.5;
    }

    .opacity-6 {
        opacity: 0.6;
    }

    .opacity-7 {
        opacity: 0.7;
    }

    .opacity-8 {
        opacity: 0.8;
    }

    .opacity-9 {
        opacity: 0.9;
    }

    .opacity-10 {
        opacity: 1;
    }

    /* Background light */
    .bg-light-1 {
        background-color: #e9ecef !important;
    }

    .bg-light-2 {
        background-color: #dee2e6 !important;
    }

    .bg-light-3 {
        background-color: #ced4da !important;
    }

    .bg-light-4 {
        background-color: #adb5bd !important;
    }

    /* Background Dark */
    .bg-dark {
        background-color: #111418 !important;
    }

    .bg-dark-1 {
        background-color: #212529 !important;
    }

    .bg-dark-2 {
        background-color: #343a40 !important;
    }

    .bg-dark-3 {
        background-color: #495057 !important;
    }

    .bg-dark-4 {
        background-color: #6c757d !important;
    }

    hr {
        opacity: 0.15;
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
        padding: 0.75rem;
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

        .table td.bg-light-1,
        .table th.bg-light-1 {
            background-color: #e9ecef !important;
        }

        .table td.bg-light-2,
        .table th.bg-light-2 {
            background-color: #dee2e6 !important;
        }

        .table td.bg-light-3,
        .table th.bg-light-3 {
            background-color: #ced4da !important;
        }

        .table td.bg-light-4,
        .table th.bg-light-4 {
            background-color: #adb5bd !important;
        }
    }

    /* =================================== */
    /*  Layouts
/* =================================== */
    .invoice-container {
        margin: 15px auto;
        padding: 70px;
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
    .btn-primary {
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bs-themecolor);
        --bs-btn-border-color: var(--bs-themecolor);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: var(--bs-themehovercolor);
        --bs-btn-hover-border-color: var(--bs-themehovercolor);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: var(--bs-themehovercolor);
        --bs-btn-active-border-color: var(--bs-themehovercolor);
    }

    .btn-outline-primary {
        --bs-btn-color: var(--bs-themecolor);
        --bs-btn-border-color: var(--bs-themecolor);
        --bs-btn-hover-bg: var(--bs-themecolor);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-border-color: var(--bs-themecolor);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: var(--bs-themehovercolor);
        --bs-btn-active-border-color: var(--bs-themehovercolor);
    }

    .progress,
    .progress-stacked {
        --bs-progress-bar-bg: var(--bs-themecolor);
    }

    .pagination {
        --bs-pagination-active-bg: var(--bs-themecolor);
        --bs-pagination-active-border-color: var(--bs-themecolor);
    }

    /* Pagination */
    .page-link {
        border-color: #f4f4f4;
        border-radius: 0.25rem;
        margin: 0 0.3rem;
    }

    .page-item.disabled .page-link {
        border-color: #f4f4f4;
    }
</style>

</html>