<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="discription" content="Digital Ideas" />
    <title>Workshop Detail</title>
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


                    <h4><strong>INVOICE ID </strong>#{{ $invoice->workshop_id }}</h4>
                    @if($workshopTyreData && $workshopTyreData->isNotEmpty())
                        @php $tyre = $workshopTyreData->first(); @endphp
                        <h5 class="mt-2"><strong>{{ strtoupper(str_replace('_',' ',$tyre->fitting_type)) }}</strong></h5>
                    @endif

                    @if($invoice->payment_status == 1)
                        <span class="badge-green">Paid</span>
                    @else

                        <span class="badge-red">Unpaid</span>
                    @endif
                </td>
            </tr>
        </table>

        @php
if (isset($invoice->workshop_date)) {
    $workshop_date = strtotime($invoice->workshop_date);
    $workshop_date = date('d/m/Y', $workshop_date);
} else {
    $workshop_date = "";
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
                        <strong>Invoice Date:</strong> {{ $invoice->created_at }} &nbsp;&nbsp;
                        <strong>Job Date:</strong> {{ $invoice->workshop_date }} &nbsp;&nbsp;
                        <strong>Due Date:</strong> {{ $invoice->due_out }} &nbsp;&nbsp;
                        <strong>Job ID #</strong>{{ $invoice->workshop_id }}
                    </div>
                </td>
            </tr>
        </table>

        <table width="100%">
            <tr>
                <td>
                    <strong>Bill To</strong>
                    <div>
                        @if ($invoice->name)
                            <strong>{{ $invoice->name }}</strong><br />
                        @endif
                        @if ($invoice->company_name)
                        Company Name:<strong>{{ $invoice->company_name }}</strong><br />
                        @endif
                        @if ($invoice->reference)
                        Reference:<strong>{{ $invoice->reference }}</strong><br />
                        @endif
                        @if ($invoice->mobile)
                            Telephone: {{ $invoice->mobile }}<br />
                        @endif
                        @if ($invoice->email)
                         Email: {{ $invoice->email }}<br />
                        @endif
                        @if ($invoice->address)
                            Addres: {{ $invoice->address }}, {{ $invoice->city }}, {{ $invoice->county }},
                            <div class="no-wrap">{{ $invoice->zone }}</div>
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
            <h4>INVOICE</h4>
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
                    <td class="text-uppercase"> {{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_reg_number
    ? $workshopVehicleData[0]->vehicle_reg_number
    : ($getIndivisualWorkshopDetail['vehicle_reg_number'] ?? '--') }}</td>
                    <td>{{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_make
    ? $workshopVehicleData[0]->vehicle_make
    : ($getIndivisualWorkshopDetail['vehicle_make'] ?? '--') }}</td>
                    <td> {{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_model
    ? $workshopVehicleData[0]->vehicle_model
    : ($getIndivisualWorkshopDetail['vehicle_model'] ?? '--') }} / {{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_year
    ? $workshopVehicleData[0]->vehicle_year
    : ($getIndivisualWorkshopDetail['vehicle_first_registered'] ?? '--') }}</td>
    <td>{{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_first_registered
    ? \Carbon\Carbon::parse($workshopVehicleData[0]->vehicle_first_registered)->format('d/m/Y')
    : '--' }} </td>
                    <td>{{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_vin
    ? $workshopVehicleData[0]->vehicle_vin
    : ($getIndivisualWorkshopDetail['vehicle_vin'] ?? '--') }} </td>
                    <td>{{ $invoice->mileage ? $invoice->mileage : '--' }}</td>
                    <td>{{ $workshopVehicleData->isNotEmpty() && $workshopVehicleData[0]->vehicle_mot_expiry_date
    ? \Carbon\Carbon::parse($workshopVehicleData[0]->vehicle_mot_expiry_date)->format('d/m/Y')
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

        @if (!empty($workshopTyreData) || !empty($workshopServiceData))
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

                    @foreach($workshopTyreData as $tyre)
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
                                 @if($tyre->fitting_type === 'mobile_fitted' || 'mailorder')
                                <tr>
                                <td>
                                     <td><strong>{{ str_replace('_', ' ', ucfirst($tyre->fitting_type)) }} CallOut Charge({{$tyre->shipping_postcode}})</strong></td>
                                </td>
                                        <td colspan="4" align="right"><strong>£{{ number_format($tyre->shipping_price, 2) }}</strong></td>
                                         <td colspan="5" align="right"><strong>£{{ number_format($tyre->shipping_price+($tyre->shipping_price*$vatRate), 2) }}</strong></td>
                                </tr>
                                @endif
                    @endforeach

                    @foreach($workshopServiceData as $service)
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
        @if (!empty($workshopTyreData) && isset($tyre))
                        <table width="100%" border="1" cellspacing="0" cellpadding="6" class="table table-bordered">
                            <thead class="bg-gray">
                                <tr>
                                    <td><strong>Sub Total</strong></td>
                                    @if($tyre->fitting_type === 'mobile_fitted' || 'mailorder')
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
                                    @if($tyre->fitting_type === 'mobile_fitted' || 'mailorder')
                                        <td>£{{ number_format($tyre->shipping_price, 2) }}</td>
                                    @endif

                                    <td>£{{ number_format($shippingVatPrice, 2) }}</td>
                                    <td>£{{ number_format($grandTotal, 2) }}</td>
                                    <td>£{{ number_format($discount_price, 2) }}</td>
                                    <td><strong>£{{ number_format($invoice->balance_price, 2) }}</strong></td>
                                    <td align="right"><strong>£{{ number_format($invoice->paid_price, 2) }}</strong></td>
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
                                    <td><strong>Discount{{ $invoice->formatted_discount }}</strong></td>
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
                                    <td>£{{ number_format($invoice->discount_price, 2) }}</td>
                                    <td><strong>£{{ number_format($invoice->balance_price, 2) }}</strong></td>
                                    <td align="right"><strong>£{{ number_format($invoice->paid_price, 2) }}</strong></td>
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