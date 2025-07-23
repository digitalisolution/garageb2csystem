@if($transactions->isEmpty())
	<p>No transactions found for this customer.</p>
@else
	<div class="container-fluid invoice-container">
		<table width="100%" style="border:solid 0px rgba(0,0,0,0.2);padding:0;">
            <tr>
                <td valign="bottom" width="70%">
                	<address>
						<h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
						Registration No: {{$garage->company_number}}<br>
						VAT Number: {{$garage->vat_number}}<br>
						Telephone: {{$garage->phone}}<br>
						Email: {{$garage->email}}<br>
						{{$garage->street}},{{$garage->city}},{{$garage->zone}},{{$garage->country}}
					</address>
                </td>
				 <?php
// Get the current domain
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
// Set the path for domain-specific logo if it exists
$domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}");
$themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}");
$defaultLogoPath = public_path("frontend/themes/theme/img/logo/logo.png");
                ?>
                <td align="right">
                	<address class="text-sm-end">
                		<div>
							@if(!empty($garage->logo))
                        <img id="logo" src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Logo" width="auto" height="60" /><br>
                    @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                        <img id="logo" src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Logo" width="auto" height="60" /><br>
                    @else

                        <img id="logo" src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}" title="Garage Name"
                             alt="Logo" width="auto" height="60" /><br>
                    @endif
						</div>
						
					</address>
                </td>
            </tr>
        </table>
        <div style="text-align:center;margin-top:30px;text-transform:uppercase;"><h2 class="m-0"><strong>Statement</strong></h2></div>
		<table width="100%" style="border:solid 0px rgba(0,0,0,0.2);padding:40px; padding-right:0;">
            <tr>
                <td valign="bottom" width="50%">
                	<address>
						<h4 class="text-4 mb-1">{{ $customer->customer_name }}</h4>
						Telephone: {{ $customer->customer_contact_number }}<br>
						Email: {{ $customer->customer_email }}<br>
						Address: {{ $customer->customer_address }}
					</address>
                </td>
                <td align="right" width="50%">
                	<address class="text-sm-end">
						<h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
						Telephone: {{$garage->phone}}<br>
						Email: {{$garage->email}}<br>
						{{$garage->street}},{{$garage->city}},{{$garage->zone}},{{$garage->country}} 
					</address>
                </td>
            </tr>
        </table>
        <div style="text-align:center;margin:20px 0;">
			<h4 class="ac_summary">Account Summary:
				<span id="summary_range">{{ now()->startOfMonth()->format('d/m/Y') }} to
					{{ now()->endOfMonth()->format('d/m/Y') }}</span>
			</h4>
		</div>

		<!-- Account Summary Table -->
		<div class="table-responsive">
			<table class="table border" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
				<thead class="thead-dark">
					<tr class="bg-light">
						<th>Beginning Balance</th>
						<th>Invoiced Amount</th>
						<th>Amount Paid</th>
						<th>discount Price</th>
						<th>Balance Due</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="no-wrap">£0.00</td>
						<td>£<span id="totalInvoiced">{{ number_format($totalInvoiced, 2) }}</span></td>
						<td>£<span id="totalPaid">{{ number_format($totalPaid, 2) }}</span></td>
						<td>£<span id="discountPrice">{{ number_format($discountPrice, 2) }}</span></td>
						<td>£<span id="balanceDue">{{ number_format($balanceDue, 2) }}</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Transactions Table -->
		<div class="table-responsive">
			<table class="table border" border="0" cellspacing="0" cellpadding="0">
				<thead class="thead-dark">
					<tr class="bg-light">
						<th>Date</th>
						<th>Details</th>
						<th>Type</th>
						<th>Amount</th>
						<th style="text-align:right;">Paid Amount</th>
						<th style="text-align:right;">Balance Amount</th>
					</tr>
				</thead>
				<tbody id="transactionsTable">
					@php $runningBalance = 0; @endphp
					@foreach($transactions as $transaction)
						@php $runningBalance += $transaction['balance_price']; @endphp
						<tr>
							<td class="no-wrap">{{ $transaction['date'] }}</td>
							<td>{{ $transaction['details'] }}</td>
							<td>{{ $transaction['type'] }}</td>
							<td>£{{ number_format($transaction['amount'], 2) }}</td>
							<td style="text-align:right;">£{{ number_format($transaction['paid_price'], 2) }}</td>
							<td style="text-align:right;">£{{ number_format($transaction['balance_price'], 2) }}</td>
						</tr>
					@endforeach

					<tr>
						<td colspan="5" style="text-align:right;"><strong>Balance Due</strong></td>
						<td style="text-align:right;"><strong>£{{ number_format($balanceDue, 2) }}</strong></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
@endif

<style>
.bg-light {
    background-color: #f8f9fa !important;
}
.border {
    border: 1px solid #dee2e6 !important;
}
.table {
    width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;border:0;padding:0;
}
.table th, .table td {
    padding: 0.4rem; border: 1px solid #dee2e6 !important;text-align:center;
}
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