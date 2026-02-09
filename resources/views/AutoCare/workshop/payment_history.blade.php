<!DOCTYPE html>
<html>

<head>
  <script src="{{ asset('js/jQuery.min.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('bootstrap-4.1.3/dist/css/bootstrap.css') }}">
  <title>Payment History</title>
  <style>
    body {
      background: #e7e9ed;
      font-size: 14px;
      line-height: 22px;
    }

    .img-bank {
      max-width: 100%;
      height: auto;
    }

    .payment-container {
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

    .payment-heading {
      background: #000;
      color: #fff;
      text-align: center;
      font-weight: 600;
      text-transform: uppercase;
      width: fit-content;
      margin: auto;
      padding: 8px 15px;
      font-size: 2em;
      margin-bottom: 30px;
    }
  </style>
</head>

<body>
  <div class="payment-container">
    <div class="payment-heading">Payment History</div>
    <header>
      <div class="row align-items-center gy-3">
        <div class="col-sm-8">
          <h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
          {{$garage->street}}{{$garage->city}}{{$garage->zone}}{{$garage->country}}</br>
          Telephone: {{$garage->mobile}}, Email: {{$garage->email}}<br>
          VAT Number: {{$garage->vat_number}}<br>
          Registration No: {{$garage->company_number}}
        </div>
        <div class="col-sm-4 text-right">
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
        <img id="logo" src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}" title="Garage Name"
        alt="Garage Name" class="img-bank" /><br>
      @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
      <img id="logo" src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}"
      title="Garage Name" alt="Garage Name" class="img-bank" /><br>
    @else
      <img id="logo" src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}" title="Garage Name"
      alt="Garage Name" class="img-bank" /><br>
    @endif
     <br>
        @if($AdminSaleView)
          <strong>Job ID:</strong> {{$AdminSaleView['0']['job_id'] }}
          @endif
        </div>
      </div>
    </header>
    <hr class="mt-5 mb-5">
    <main>
      <div class="row">
        @if($AdminSaleView)
        <div class="col-sm-12 text-center">
          <h5>Customer Details:</h5>
          <address class="mt-2">
            <strong>{{$AdminSaleView['0']['customer_name'] }}</strong><br>
            {{$AdminSaleView['0']['address'] }},{{$AdminSaleView['0']['city'] }},<br>{{$AdminSaleView['0']['county'] }},{{$AdminSaleView['0']['zone'] }},{{$AdminSaleView['0']['country'] }}</strong><br>
            Telephone: {{$AdminSaleView['0']['customer_contact_number'] }},
            Email:{{$AdminSaleView['0']['customer_email'] }}
          </address>
        </div>
        @endif

      </div>
    </main>

    <div class="table-responsive">
      <table class="table border">
        <thead>
          <tr class="bg-light">
            <th style="white-space: nowrap">Job ID</th>
            <th style="white-space: nowrap">Payment Date</th>
            <th style="white-space: nowrap">Payment Amount</th>
            <th style="white-space: nowrap">Payment Type</th>
          </tr>
        </thead>
       <tbody>
        @if($AdminSaleView)
    @php $p = 0; @endphp
    @foreach($AdminSaleView as $key => $value)
        <tr>
            @php $p++; @endphp
            <td>JOB-{{ $value['job_id'] }}</td>
            <td>{{ $value['created_at'] }}</td>
            <td>£{{ number_format($value['payment_amount'], 2, '.', '') }}</td>
            <td>
                @switch($value['payment_type'])
                    @case(1)
                        By Cash
                        @break
                    @case(2)
                       By Card
                        @break
                    @case(3)
                        By Check
                        @break
                    @case(4)
                        By Bank Transfer
                        @break
                    @default
                        Unknown
                @endswitch
            </td>
        </tr>
    @endforeach
    @else
    <tr><td>
No Payment Record Found
    </td> </tr>
    @endif
</tbody>

        <tfoot>
        </tfoot>
      </table>
    </div>
    <div class="text-right mt-5">
      <br>
      Signature
    </div>
</body>

</html>