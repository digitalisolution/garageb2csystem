<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
</head>

<body>
@php
$status =  strtoupper($workshop->status);
@endphp 
  <div style="margin:0;padding:0;width:100%;background-color:#F0F0F0;" marginwidth="0" marginheight="0">
    <table style="width:100%!important">
      <tbody>
        <tr width="834px" height="50">
          <td style="background:#272727;">
            <table width="100%" cellspacing="0" cellpadding="0" height="50"
              style="width:600px!important;text-align:center;margin:0 auto">
              <tbody>
                <tr>
                  <td>
                    <table style="width:640px;max-width:640px;padding-right:20px;padding-left:20px">
                      <tbody>
                        <tr>
                          <td style="width:100%;text-align:center;padding-top:5px">
                            <p
                              style="color:rgba(255,255,255,0.8);font-family:Arial;font-size:16px;text-align:center;color:#ffffff;font-style:normal;font-stretch:normal">
                              Order <span style="font-weight:bold">{{$status}}</span></p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#f5f5f5">
              <tbody>
                <tr>
                  <td align="center" valign="top" bgcolor="#f5f5f5">
                    <table border="0" cellpadding="0" cellspacing="0"
                      style="width:640px;max-width:640px;padding-right:20px;padding-left:20px;background-color:#fff;padding-top:20px">
                      <tbody>
                        <tr>
                          <td align="left">
                            <table width="350" border="0" cellpadding="0" cellspacing="0" align="left">
                              <tbody>
                                <tr>
                                  <td valign="top">
                                    <p
                                      style="font-family:Arial;color:#878787;font-size:12px;font-weight:normal;font-style:normal;font-stretch:normal;margin-top:0px;line-height:14px;padding-top:0px;margin-bottom:7px">
                                      Hi <span style="font-weight:bold;color:#191919"> {{ $workshop->name }},</span></p>
                                    <p
                                      style="font-family:Arial;font-size:12px;color:#878787;line-height:14px;padding-top:0px;margin-top:0px;margin-bottom:7px">
                                      Your order has been <strong>{{ $status}}</strong>.</p>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            <table width="250" border="0" cellpadding="0" cellspacing="0" align="right">
                              <tbody>
                                <tr>
                                  <td valign="top">
                                    <p
                                      style="font-family:Arial;font-size:11px;color:#878787;line-height:14px;text-align:right;padding-top:0px;margin-top:0;margin-bottom:7px">
                                      Order ID <span style="font-weight:bold;color:#000">{{ $workshop->id }}</span></p>
                                    <p
                                      style="font-family:Arial;color:#747474;font-size:11px;font-weight:normal;text-align:right;font-style:normal;line-height:14px;font-stretch:normal;margin-top:0px;padding-top:0px;color:#878787;margin-bottom:7px">
                                      Workshop Status <span
                                        style="font-weight:bold;color:#000">({{ $status }})</span></p>
                                    @if ($workshopProducts->isNotEmpty())
                    <p
                      style="font-family:Arial;color:#747474;font-size:11px;font-weight:normal;text-align:right;font-style:normal;line-height:14px;font-stretch:normal;margin-top:0px;padding-top:0px;color:#878787;margin-bottom:7px">
                      Order Products <span style="font-weight:bold;color:#000">
                        ({{ strtoupper(str_replace('_',' ',$workshopProducts->first()->fitting_type ?? '' ))}})</span></p>
                  @endif
                                  </td>
                                </tr>

                              </tbody>
                            </table>
                          </td>
                        </tr>


                        <tr>
                          <td>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top:20px;">
                              <tbody>
                                <!-- Order Products -->
                                @if ($workshopProducts->isNotEmpty())
                                  <tr>
                                    <td colspan="4" style="text-align: left; font-weight: bold; font-size:17px;">
                                    Order Products
                                    </td>
                                  </tr>
                                  <tr>
                                    <td align="left">
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left">
                                      <thead>
                                      <th
                                        style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;line-height:20px;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:70%;text-align:left;font-weight:bold;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;">
                                        Description</th>
                                      <th
                                        style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;line-height:20px;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:center;font-weight:bold;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;">
                                        Qty</th>
                                      <th
                                        style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;line-height:20px;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:right;font-weight:bold;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;">
                                        Price</th>
                                      <th
                                        style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;line-height:20px;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:right;font-weight:bold;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;">
                                        Total</th>
                                      </thead>
                                      <tbody>
                                      <tr>

@php
    $totalAmount = 0;
    $subTotal = 0;
    $vatValue = 0;
    $calloutCharges = 0; // Track total callout charges
    $calloutVat = 0; // Track total VAT for callout charges
    $calloutAdded = false; // Flag to ensure callout charges are added only once
@endphp

@foreach ($workshopProducts as $product)
    @php
        // Initialize variables for this product
        $itemTotal = 0;

        if ($product->product_type === 'tyre') {
            // Calculate item total for tyres
            $itemTotal = $product->price * $product->quantity;

            // Handle callout charges for mobile-fitted tyres (add only once per job)
            if ($product->fitting_type === 'mobile_fitted' && !$calloutAdded) {
                $shippingPrice = $product->shipping_price ?? 0;
                $calloutCharges += $shippingPrice; // Add to callout charges

                // Calculate VAT for shipping if shipping_tax_id is 9
                if ($product->shipping_tax_id == 9) {
                    $shippingVat = $shippingPrice * 0.20; // 20% VAT
                    $calloutVat += $shippingVat; // Add to total VAT
                }

                $calloutAdded = true; // Mark callout charges as added
            }
        } elseif ($product->product_type === 'service') {
            // Calculate item total for services
            $itemTotal = $product->service_price * $product->service_quantity;

            // Handle callout charges for jobs (add only once per job)
            if ($product->fitting_type === 'mobile_fitted' && !$calloutAdded) {
                $shippingPrice = $product->shipping_price ?? 0;
                $calloutCharges += $shippingPrice; // Add to callout charges

                // Calculate VAT for shipping if shipping_tax_id is 9
                if ($product->shipping_tax_id == 9) {
                    $shippingVat = $shippingPrice * 0.20; // 20% VAT
                    $calloutVat += $shippingVat; // Add to total VAT
                }

                $calloutAdded = true; // Mark callout charges as added
            }
        }

        // Calculate VAT for the product itself
        if ($product->tax_class_id == 9) {
            $currentVatValue = $itemTotal * 0.20; // 20% VAT
            $vatValue += $currentVatValue;
        }

        // Update sub-total (excluding callout charges)
        $subTotal += $itemTotal;
    @endphp

    <!-- Product Row -->
    <tr>
        <td valign="middle" align="left"
            style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:70%;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;vertical-align:middle;height:65px;">
            {{ $product->description ?? $product->service_name ?? 'No description' }}<br>
            <small>
                @if($product->product_type === 'tyre')
                  ({{ $product->supplier }}), EAN: ({{ $product->product_ean ?? $product->product_sku }})
                    @if($product->fitting_type === 'mobile_fitted')
                    @endif
                @endif
            </small>
        </td>
        <td valign="middle" align="left"
            style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:center;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;vertical-align:middle;height:65px;">
            @if($product->product_type === 'tyre')
                {{ $product->quantity ?? '0' }}
            @elseif($product->product_type === 'service')
                {{ $product->service_quantity ?? '0' }}
            @endif
        </td>
        <td valign="middle" align="left"
            style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:right;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;vertical-align:middle;height:65px;">
            £{{ number_format($product->price ?? $product->service_price ?? 0, 2) }}
        </td>
        <td valign="middle" align="left"
            style="font-family:Arial;font-size:14px;font-weight:normal;font-style:normal;font-stretch:normal;color:#212121;text-decoration:none!important;word-spacing:0.2em;display:inline-block;width:10%;text-align:right;border-bottom:solid 1px #ccc;padding-top:5px;padding-bottom:5px;vertical-align:middle;height:65px;">
            £{{ number_format($itemTotal, 2) }}
        </td>
    </tr>
@endforeach
                                  </tr>
                                  </tbody>
                                </table>
                                </td>
                              </tr>
                              </tbody>
                            </table>
                            </td>
                          </tr>
                          <tr>
                            <td>
                            <table border="0" width="600" cellpadding="0" cellspacing="0">
                              <tbody>
                              <tr>
                                <td>
                                <table width="100%" cellspacing="0" cellpadding="0"
                                  style="margin:0;padding-top:20px;padding-bottom:20px;max-width:600px;background:#ffffff">
                                  <tbody>
                                  <tr style="color:#212121;display:block;margin:0 auto;clear:both">
                                    <td align="left" valign="top" style="color:#212121;display:block">
                                    <table width="100%" style="margin-bottom:0px;border-bottom:1px solid #f0f0f0">
                                      <tbody>
                                      <tr>
                                        <td width="40%"></td>
                                        <td align="right" width="34%">
                                        <p
                                          style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                          <span style="color:#3f3f3f;text-align:right">Sub Total</span></p>
                                        </td>
                                        <td>
                                        <p
                                          style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                          <span style="padding-right:0px">£{{ number_format($subTotal, 2) }} </span>
                                        </p>
                                        </td>
                                      </tr>
                                        @if ($workshopProducts->contains('fitting_type', 'mobile_fitted'))
                                        <tr>
                                        <td width="40%"></td>
                                        <td align="right" width="34%">
                                        <p
                                        style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                        <span style="color:#3f3f3f;text-align:right">Callout Charges ({{ $workshopProducts->firstWhere('fitting_type', 'mobile_fitted')->shipping_postcode ?? 'N/A' }})</span>
                                        </p>
                                        </td>
                                        <td>
                                        <p
                                        style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                        <span style="padding-right:0px">£{{ number_format($calloutCharges, 2) }}</span>
                                        </p>
                                        </td>
                                        </tr>
                                        @endif

                                      <tr>
                                        <td width="40%"></td>
                                        <td align="right" width="34%">
                                        <p
                                          style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                          <span style="color:#3f3f3f;text-align:right">VAT</span></p>
                                        </td>
                                        <td>
                                        <p
                                          style="margin-top:14px;font-family:Arial;font-size:12px;text-align:right;color:#3f3f3f;padding-top:0px;margin-top:0;margin-bottom:3px">
                                          <span style="padding-right:0px">£{{ number_format($vatValue + $calloutVat, 2) }} </span>
                                        </p>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td width="40%"></td>
                                        <td align="right" width="34%">
                                        <p
                                          style="margin-top:0px;font-family:Arial;font-size:14px;text-align:right;color:#3f3f3f;line-height:14px;padding-top:0px;margin-bottom:0">
                                          <span style="color:#212121;text-align:right;font-weight:bold">Total
                                          Amount</span></p>
                                        </td>
                                        <td>
                                        <p
                                          style="margin-top:0px;font-family:Arial;font-size:14px;text-align:right;color:#3f3f3f;padding-top:0px;margin-bottom:0">
                                          <span
                                          style="padding-right:0px;font-weight:bold">£{{ number_format($subTotal + $calloutCharges + $vatValue + $calloutVat, 2) }}
                                          </span></p>
                                        </td>
                                      </tr>
                @endif
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td border="1" align="left"
                    style="background-color:rgba(245,245,245,0.5);background:rgba(245,245,245,0.5);border:.5px solid #ccc;border-radius:2px;padding-top:20px;padding-bottom:20px;border-color:#ccc;border-width:.08em;border-style:solid;border:.08em solid #ccc">
                    <table width="300" border="0" cellpadding="0" cellspacing="0" align="left"
                      style="margin-bottom:20px;padding-left:15px">
                      <tbody>
                        <tr>
                          <td valign="top">
                            <div style="max-width:290px;padding-top:0px;margin-bottom:20px">
                              <p
                                style="font-family:Arial;font-size:14px;font-weight:bold;line-height:20px;color:#212121;margin-top:0px;margin-bottom:4px">
                                Customer Details</p>
                              <p
                                style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121;margin-top:0px;margin-bottom:0">
                                Name: {{ $workshop->name }} <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Address:
                                  {{ $workshop->address }} </span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Contact:
                                  {{ $workshop->mobile }} </span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Email:
                                  {{ $workshop->email }} </span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Vehicle
                                  Details: <span
                                    style="text-transform:uppercase;">({{ $workshop->vehicle_reg_number }})</span>
                                </span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Appointment
                                  Details: @if($bookings->isNotEmpty())
                    @foreach($bookings as $booking)
            Start: {{ \Carbon\Carbon::parse($booking->start)->format('Y-m-d H:i') }}<br>
            End: {{ \Carbon\Carbon::parse($booking->end)->format('Y-m-d H:i') }}
          @endforeach
                  @else
            No Appointments Found
          @endif </span><br>                               <p
                                style="font-family:Arial;font-size:14px;font-weight:bold;line-height:20px;color:#212121;margin-top:0px;margin-bottom:4px;margin-top:15px;">
                                Payment Method</p>
                                <strong>Payment Method:</strong>
                                {{ str_replace('_',' ',$workshop->payment_method) ?? 'Pay at Fitting Center' }}<br>
                                <strong>Payment Status:</strong>
                                {{ $workshop->payment_status === 1 ? 'Paid' : 'Unpaid' }}<br>
                                <strong>Fitting Address:</strong><br>
                                @if ($workshopProducts->isNotEmpty() && $workshopProducts->contains('fitting_type', 'mobile_fitted'))
                                    <!-- Workshop Address (Mobile Fitting) -->
                                    {{ $workshop->address }}<br>
                                    {{ $workshop->city }},{{ $workshop->county }}, {{ $workshop->zone }}, {{ $workshop->country }}
                                @else
                                    <!-- Garage Address (Default) -->
                                    {{ $garage->garage_name }}<br>
                                    {{ $garage->street }}, {{ $garage->city }},{{ $garage->county }}, {{ $garage->zone }}, {{ $garage->country }}
                                    <br>
                                  @endif
                              </p>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table width="300" border="0" cellpadding="0" cellspacing="0" align="right"
                      style="margin-bottom:20px;padding-right:15px">
                      <tbody>
                        <tr>
                          <td valign="top" align="left">
                            <div style="max-width:290px;padding-top:0px;margin-bottom:20px">
                              <p
                                style="font-family:Arial;font-size:14px;font-weight:bold;line-height:20px;color:#212121;margin-top:0px;margin-bottom:4px">
                                Garage Details</p>
                              <p
                                style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121;margin-top:0px;margin-bottom:0">
                                Name: {{ $garage->garage_name }} <br>
                                <span style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121"> Address:
                                  {{ $garage->street }}, {{ $garage->city }}, {{ $garage->Zone }},
                                  {{ $garage->country }}</span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Telephone:
                                  {{ $garage->mobile }} </span>
                                <br> <span
                                  style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">Email:
                                  {{ $garage->email }} </span>
                                <br> <span style="font-family:Arial;font-size:12px;line-height:1.42;color:#212121">URL:
                                  {{ $garage->website_url }} </span>
                              </p>
                              <p
                                style="font-family:Arial;font-size:14px;font-weight:bold;line-height:20px;color:#212121;margin-top:0px;margin-bottom:4px;margin-top:15px;">
                                Opening Time</p>
                              @if(!empty($garage->garage_opening_time))
                  @foreach(explode(',', $garage->garage_opening_time) as $openingTime)
            {{ trim($openingTime) }}<br>
          @endforeach
                @else
            No opening hours available
          @endif
                            </div>

                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="left"
                      style="padding-left:15px;padding-right:15px"></table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table border="0" width="600" cellpadding="0" cellspacing="0"
              style="padding-right:20px;padding-left:20px;background-color:#fff;width:640px;max-width:640px">
              <tbody>
                <tr>
                  <td>
                    <table width="100%" cellspacing="0" cellpadding="0"
                      style="width:600px;max-width:600px;background:#ffffff">
                      <tbody>
                        <tr style="color:#212121">
                          <td align="center" valign="top" style="color:#212121;">
                            <p
                              style="font-family:Arial;font-size:14px;font-weight:bold;line-height:1.86;color:#212121;padding-top:15px;">
                              Thank you for choosing our service!</p><br>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    </td>
    </tr>
    </tbody>
    </table>
  </div>
</body>

</html>