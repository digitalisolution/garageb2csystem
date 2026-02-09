<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment For #{{$workshop->id }}</title>
</head>

<body class="gateway-2checkout">
    <div class="container">
        <div class="col-md-8 col-md-offset-2 mtop30">
            <div class="mbot30 text-center">
                LOGO
            </div>
            <div class="row">
                <div class="panel_s">
                    <div class="panel-body">
                        <hr />
                        <p><span class="bold">payment_total:- {{ $workshop->grandTotal}}</span></p>
                        <form action="{{ $action }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="MERCHANT_ID" value="{{ $merchantId }}">
                            <input type="hidden" name="ORDER_ID" value="{{ $orderId }}">
                            <input type="hidden" name="CURRENCY" value="{{ $currency }}">
                            <input type="hidden" name="AMOUNT" value="{{ $amount }}">
                            <input type="hidden" name="TIMESTAMP" value="{{ $timestamp }}">
                            <input type="hidden" name="SHA1HASH" value="{{ $hash }}">
                            <input type="hidden" name="AUTO_SETTLE_FLAG" value="1">
                            <input type="hidden" name="RETURN_TSS" value="0">
                            <input type="hidden" name="BILLING_CODE" value="33">
                            <input type="hidden" name="HPP_BILLING_CITY" value="{{ $workshop->city ?? '' }}">
                            <input type="hidden" name="HPP_BILLING_COUNTRY" value="826">
                            <input type="hidden" name="HPP_BILLING_STREET1" value="{{ $workshop->address ?? '' }}">
                            <input type="hidden" name="HPP_BILLING_POSTALCODE" value="{{ $workshop->zone ?? '' }}">
                            <input type="hidden" name="HPP_CUSTOMER_EMAIL" value="{{ $workshop->email }}">
                            <input type="hidden" name="HPP_CUSTOMER_PHONE" value="{{ $workshop->mobile }}">
                            <input type="hidden" name="BILLING_CO" value="GB">
                            <input type="hidden" name="SHIPPING_CODE" value="33">
                            <input type="hidden" name="SHIPPING_CO" value="GB">
                            <input type="hidden" name="MERCHANT_RESPONSE_URL" value="{{ $successUrl }}">
                            <input type="hidden" name="COMMENT1" value="Invoice Payment">

                            <button type="submit" class="btn btn-info">Submit Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>