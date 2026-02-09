<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="discription" content="Digital Ideas" />
    <title>Estimate Detail</title>
</head>

<body>

    <!-- Container -->
    <div class="container-fluid invoice-container">
        <table width="100%">
            <tr>
                <td>
                    <?php
// Get the current domain
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
// Set the path for domain-specific logo if it exists
$domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}");
$themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}");
$defaultLogoPath = public_path("frontend/themes/theme/img/logo/logo.png");
                    ?>


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
                    <!--<h4 class="text-5 mb-2">{{$garage->garage_name}}</h4>
                    {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}</br>
                    Telephone: {{$garage->mobile}}, Email: {{$garage->email}}<br>
                    @if($garage->vat_number)
                        VAT Number: {{$garage->vat_number}}<br>
                    @endif
                    @if($garage->vat_number)
                        Registration No: {{$garage->company_number}}
                    @endif-->
                </td>
                <td align="right">


                    <h4><strong>ESTIMATE ID </strong>#{{ $estimate->id }}</h4>
                    @if($estimateTyreData && $estimateTyreData->isNotEmpty())
                        @php $tyre = $estimateTyreData->first(); @endphp
                        <h5 class="mt-2"><strong>{{ strtoupper(str_replace('_',' ',$tyre->fitting_type)) }}</strong></h5>
                    @endif

                    @if($estimate->payment_status == 1)
                        <span class="badge-green">Paid</span>
                    @else

                        <span class="badge-red">Unpaid</span>
                    @endif
                </td>
            </tr>
        </table>

        @php
if (isset($estimate->workshop_date)) {
    $estimate_date = strtotime($estimate->workshop_date);
    $estimate_date = date('d/m/Y', $estimate_date);
} else {
    $estimate_date = "";
}
if (isset($created_at)) {
    $created_at = strtotime($created_at);
    $created_at = date('d/m/Y', $created_at);
} else {
    $created_at = "";
}
if (isset($due_out)) {
    $due_out = strtotime($due_out);
    $due_out = date('d/m/Y', $due_out);
} else {
    $due_out = "";
}
        @endphp
        <table width="100%">
            <tr>
                <td>
                    <div class="ingray_strip">
                    <strong>Estimate Date:</strong> {{ \Carbon\Carbon::parse($estimate->created_at)->format('d-m-Y') }} &nbsp;&nbsp;
                    <strong>Job Date:</strong> {{ \Carbon\Carbon::parse($estimate->workshop_date)->format('d-m-Y') }} &nbsp;&nbsp;
                    <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($estimate->due_out)->format('d-m-Y') }} &nbsp;&nbsp;
                    @if($estimate->workshop_id)
                        <strong>Job ID #</strong>{{ $estimate->workshop_id }}
                    @endif
                </div>

                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <td>
                    <strong>Bill To</strong>
                    <div>
                        @if ($estimate->name)
                            <strong>{{ $estimate->name }}</strong><br />
                        @endif
                        @if ($estimate->company_name)
                        Company Name:<strong>{{ $estimate->company_name }}</strong><br />
                        @endif
                        @if ($estimate->reference)
                        Reference:<strong>{{ $estimate->reference }}</strong><br />
                        @endif
                        @if ($estimate->mobile)
                            Telephone: {{ $estimate->mobile }}<br />
                        @endif
                        @if ($estimate->email)
                         Email: {{ $estimate->email }}<br />
                        @endif
                        @if ($estimate->address)
                            Addres: {{ $estimate->address }}, {{ $estimate->city }}, {{ $estimate->county }},
                            <div class="no-wrap">{{ $estimate->zone }}</div>
                        @endif
                    </div>
                </td>
                <td align="right">
                <div class="mt-2 text-sm-end">
                    <h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
                    @if($garage->company_number)
                        Registration No: {{$garage->company_number}}<br>
                    @endif
                    @if($garage->vat_number)
                        VAT Number: {{$garage->vat_number}}<br>
                    @endif
                    @if($garage->eori_number)
                        EORI No: {{$garage->eori_number}}<br>
                    @endif
                    Telephone: {{$garage->mobile}}<br>
                    Email: {{$garage->email}}<br>
                    {{$garage->street}} {{$garage->city}} <div class="no-wrap">{{$garage->zone}}</div> {{$garage->country}}
                </div>
                </td>
            </tr>
        </table>
        <div style="background:#f2f2f2;text-align:center;border:solid 1px #ddd;margin-top:20px;padding:5px 10px;">
            <h4>ESTIMATE</h4>
        </div>

        <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered">
            <thead class="bg-gray">
                <tr>
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
                <tr class="bg-light">
                    <td class="text-uppercase"> {{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_reg_number
    ? $estimateVehicleData[0]->vehicle_reg_number
    : ($getIndivisualWorkshopDetail['vehicle_reg_number'] ?? '--') }}</td>
                    <td>{{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_make
    ? $estimateVehicleData[0]->vehicle_make
    : ($getIndivisualWorkshopDetail['vehicle_make'] ?? '--') }}</td>
                    <td> {{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_model
    ? $estimateVehicleData[0]->vehicle_model
    : ($getIndivisualWorkshopDetail['vehicle_model'] ?? '--') }} / {{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_year
    ? $estimateVehicleData[0]->vehicle_year
    : ($getIndivisualWorkshopDetail['vehicle_first_registered'] ?? '--') }}</td>
    <td>{{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_first_registered
    ? \Carbon\Carbon::parse($estimateVehicleData[0]->vehicle_first_registered)->format('d/m/Y')
    : '--' }} </td>
                    <td>{{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_vin
    ? $estimateVehicleData[0]->vehicle_vin
    : ($getIndivisualWorkshopDetail['vehicle_vin'] ?? '--') }} </td>
                    <td>{{ $estimate->mileage ? $estimate->mileage : '--' }}</td>
                    <td>{{ $estimateVehicleData->isNotEmpty() && $estimateVehicleData[0]->vehicle_mot_expiry_date
    ? \Carbon\Carbon::parse($estimateVehicleData[0]->vehicle_mot_expiry_date)->format('d/m/Y')
    : '--' }} </td>
                </tr>
            </tbody>
        </table>
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

        @if (!empty($estimateTyreData) || !empty($estimateServiceData))
            <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered">
                <thead class="bg-gray">
                    <tr>
                        <td><strong>#</strong></td>
                        <td><strong>Description</strong></td>
                        <td><strong>Rate</strong></td>
                        <td><strong>VAT %</strong></td>
                        <td><strong>Qty</strong></td>
                        <td align="right"><strong>Item Total</strong></td>
                        <td align="right"><strong>Amount <br>(Inc. Tax)</strong></td>
                    </tr>
                </thead>
                <tbody>

                    @foreach($estimateTyreData as $tyre)
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
                                    <td>{{ $tyre->description }} <br> ({{ $tyre->product_ean }})</td>
                                    <td align="center">£{{ number_format($price, 2) }}</td>
                                    <td>{{ $vatText }}</td>
                                    <td align="center">{{ $quantity }}</td>
                                    <td align="right">£{{ number_format($itemTotal, 2) }}</td>
                                    <td align="right"><strong>£{{ number_format($totalAmount, 2) }}</strong></td>
                                </tr>
                                 @if($tyre->fitting_type === 'mobile_fitted')
                                <tr>
                                <td>
                                        <td><strong>Mobile Fitting CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                </td>
                                        <td colspan="4" align="right"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong></td>
                                         <td colspan="5" align="right"><strong>£{{ number_format($tyre->shipping_price+($tyre->shipping_price*$vatRate), 2) }}</strong></td>
                                </tr>
                                @endif
                    @endforeach

                    @foreach($estimateServiceData as $service)
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
                                    <td align="right">£{{ number_format($itemTotal, 2) }}</td>
                                    <td align="right"><strong>£{{ number_format($totalAmount, 2) }}</strong></td>
                                </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <br />
        @if (!empty($estimateTyreData) && isset($tyre))
                        <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered">
                            <thead class="bg-gray">
                                <tr>
                                    <td><strong>Sub Total</strong></td>
                                    @if($tyre->fitting_type === 'mobile_fitted')
                                        <td><strong>CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                    @endif

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
            $shippingVatRates = $tyre->shipping_tax_id == 9 ? 0.2 : 0;
            $shippingVatRate = $tyre->shipping_price * $shippingVatRates;
            $shippingVatPrice = $total_Tax_Amount + $shippingVatRate;
            $shippingTotalPrice = $tyre->shipping_price + $shippingVatRate;

            $subTotal = $total_product_price + $total_service_price;
            $grandTotal = $subTotal + $total_Tax_Amount + $shippingTotalPrice;
            $balancePrice = $grandTotal - ($installmentPayment + $paid_price + $discount_price);
                                    @endphp
                                    <td>£{{ number_format($subTotal, 2) }}</td>
                                    @if($tyre->fitting_type === 'mobile_fitted')
                                        <td>£{{ number_format($tyre->shipping_price, 2) }}</td>
                                    @endif

                                    <td>£{{ number_format($shippingVatPrice, 2) }}</td>
                                    <td>£{{ number_format($grandTotal, 2) }}</td>
                                    <td>£{{ number_format($discount_price, 2) }}</td>
                                    <td><strong>£{{ number_format($estimate->balance_price, 2) }}</strong></td>
                                    <td align="right"><strong>£{{ number_format($estimate->paid_price, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
        @else

                        <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered mt-3">
                            <thead class="bg-gray">
                                <tr>
                                    <td><strong>Sub Total</strong></td>
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
            $subTotal = $total_product_price + $total_service_price;
            $grandTotal = $subTotal + $total_Tax_Amount;
            $balancePrice = $grandTotal - ($installmentPayment + $paid_price + $discount_price);
                                    @endphp
                                    <td>£{{ number_format($subTotal, 2) }}</td>
                                    <td>£{{ number_format($total_Tax_Amount, 2) }}</td>
                                    <td>£{{ number_format($grandTotal, 2) }}</td>
                                    <td>£{{ number_format($estimate->discount_price, 2) }}</td>
                                    <td><strong>£{{ number_format($estimate->balance_price, 2) }}</strong></td>
                                    <td align="right"><strong>£{{ number_format($estimate->paid_price, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
        @endif
        <br />
        @if($paymentHistory && $paymentHistory->isNotEmpty())
        <h5>Transactions</h5>
        <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered mt-3">
            <thead class="bg-gray">
                <tr >
                    <td><strong>Payment#</td>
                    <td><strong>Payment Mode</td>
                    <td><strong>Date</td>
                    <td align="right"><strong>Amount</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentHistory as $item)
                    <tr>
                        <td>{{ htmlspecialchars($item->id) }}</td>
                        <td>
                            @switch($item->payment_type)
                                @case(1)
                                    By Cash
                                    @break
                                @case(2)
                                    By Card
                                    @break
                                @case(3)
                                    By Cheque
                                    @break
                                @default
                                    Unknown
                            @endswitch
                        </td>
                        <td>{{ htmlspecialchars(date('d/m/Y', strtotime($item->created_at))) }}</td>
                        <td align="right"><strong>£{{ number_format($item->debit_amount, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

@endif
<br/>
        <div class="terms-conditions text-sm-start text-center">
            {!! $garage->notes !!}
        </div>
    </div>
</body>
<style>
body {background: #fff;font-size: 13px;line-height: 20px;font-family: Arial, Helvetica, sans-serif;}
h4 {margin: 0;font-size: 16px;}
h5 {margin: 0;font-size: 14px;}
h6 {margin: 0;font-size: 13px;}
p {margin-bottom: 0;margin-top: 0;line-height: normal;}
.table {width: 100%;padding: 0;margin: 0;}
.table-bordered tr td {border: solid 1px #ddd;}
.ingray_strip {background: rgba(0, 0, 0, 0.05);border: solid 1px rgba(0, 0, 0, 0.1);font-size: 12px;text-align: center;padding-bottom: 7px;margin-top: 20px;}
.bg-gray {background: rgba(0, 0, 0, 0.05);}
.terms-conditions {margin-top: 10px;}
.badge-red {background: red;color: #fff;font-weight: bold;padding: 1px 10px;border-radius: 2px;text-transform: uppercase;font-size: 80%;}
.badge-green {background: green;color: #fff;font-weight: bold;padding: 1px 10px;border-radius: 2px;text-transform: uppercase;font-size: 80%;}
.invoice-container {margin: 0 auto;padding: 20px 20px;max-width: 1000px;background-color: #fff;border: 1px solid #ccc;-moz-border-radius: 6px;-webkit-border-radius: 6px;-o-border-radius: 6px;border-radius: 6px;}
@media (max-width: 767px) {
    .invoice-container {padding: 20px;margin-top: 0px;border: none;border-radius: 0px;}
}
</style>
</html>