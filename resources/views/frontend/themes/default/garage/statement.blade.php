@extends('layouts.app')
@section('content')
	<input type="hidden" id="loadStatement" data-customer-id="{{ $garages->id }}"></input>
<div class="pt-60 pb-60">
	<div class="container">
		<div class="bg-white">
			@include('garage.menu')
			<div class="short__item">
				<div class="bg-light p-2 text-center border rounded mb-4">
					<h5 class="m-0"><strong>Statement</strong></h5>
				</div>

				<div class="statement_bank">
					<div class="item">
						<div class="form-group">
							<label for="range" class="mb-1"><strong>Select Time Period</strong></label>
							<select class="selectpicker form-control" name="range" id="range" data-width="100%"
								onchange="render_garage_statement();">
								<option value='["{{ now()->format('d-m-Y') }}", "{{ now()->format('d-m-Y') }}"]'>Today
								</option>
								<option
									value='["{{ now()->startOfWeek()->format('d-m-Y') }}", "{{ now()->endOfWeek()->format('d-m-Y') }}"]'>
									This Week</option>
								<option
									value='["{{ now()->startOfMonth()->format('d-m-Y') }}", "{{ now()->endOfMonth()->format('d-m-Y') }}"]'
									selected>This Month</option>
								<option
									value='["{{ now()->subMonth()->startOfMonth()->format('d-m-Y') }}", "{{ now()->subMonth()->endOfMonth()->format('d-m-Y') }}"]'>
									Last Month</option>
								<option
									value='["{{ now()->startOfYear()->format('d-m-Y') }}", "{{ now()->endOfYear()->format('d-m-Y') }}"]'>
									This Year</option>
								<option
									value='["{{ now()->subYear()->startOfYear()->format('d-m-Y') }}", "{{ now()->subYear()->endOfYear()->format('d-m-Y') }}"]'>
									Last Year</option>
								<option value="custom">Custom Period</option>
							</select>
						</div>

						<!-- Custom Date Range Fields (Initially Hidden) -->
						<div class="statement_bank d-none" id="customDateRange">
							<div>
								<label for="custom_from">From:</label>
								<input type="date" id="custom_from" class="form-control"
									onchange="render_garage_statement();">
							</div>
							<div>
								<label for="custom_to">To:</label>
								<input type="date" id="custom_to" class="form-control"
									onchange="render_garage_statement();">
							</div>
						</div>
					</div>

				</div>
				<!-- Customer Details -->
				<div class="border px-4 py-4">
					<div class="row">
						<div class="col-lg-5 col-md-5 col-sm-6 col-12">
							<address>
								<h4 class="text-4 mb-1">{{$garages->garage_name}}</h4>
								Registration No: {{$garages->garage_company_number}}<br>
								VAT Number: {{$garages->garage_vat_number}}<br>
								Telephone: {{$garages->garage_phone}}<br>
								Email: {{$garages->garage_email}}<br>
								{{$garages->garage_street}},{{$garages->garage_city}},{{$garage->garage_zone}},{{$garage->garage_country}}
							</address>
						</div>

						<div class="col-lg-5 col-md-5 col-sm-6 col-12 offset-md-2 text-sm-end">
							<address class="text-sm-end">
								<h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
								Registration No: {{$garage->company_number}}<br>
								VAT Number: {{$garage->vat_number}}<br>
								Telephone: {{$garage->phone}}<br>
								Email: {{$garage->email}}<br>
								{{$garage->street}},{{$garage->city}},{{$garage->zone}},{{$garage->country}}
							</address>
						</div>

					</div>
				</div>
				@if($transactions->isEmpty())
					<p>No transactions found for this Garage.</p>
				@else
					<!-- Account Summary -->
					<div class="text-center my-3">
						<h4 class="ac_summary">Account Summary:
							<span id="summary_range">{{ now()->startOfMonth()->format('d/m/Y') }} to
								{{ now()->endOfMonth()->format('d/m/Y') }}</span>
						</h4>
					</div>

					<!-- Account Summary Table -->
					<div class="table-responsive">
						<table class="table table-striped border">
							<thead class="bg-dark text-white">
								<tr>
									<th>Beginning Balance</th>
									<th>Invoiced Amount</th>
									<th class="text-right">Amount Paid</th>
									<th class="text-right">Balance Due</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="no-wrap">£0.00</td>
									<td>£<span id="totalInvoiced">{{ number_format($totalInvoiced, 2) }}</span></td>
									<td class="text-right">£<span id="totalPaid">{{ number_format($totalPaid, 2) }}</span></td>
									<td class="text-right">£<span id="balanceDue">{{ number_format($balanceDue, 2) }}</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<!-- Transactions Table -->
					<div class="table-responsive">
						<table class="table table-striped border">
							<thead class="bg-dark text-white">
								<tr>
									<th>Date</th>
									<th>Details</th>
									<th>Type</th>
									<th class="text-right">Amount</th>
									<th class="text-right">Paid Amount</th>
									<th class="text-right">Balance Amount</th>
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
										<td class="text-right">£{{ number_format($transaction['amount'], 2) }}</td>
										<td class="text-right">£{{ number_format($transaction['paid_price'], 2) }}</td>
										<td class="text-right">£{{ number_format($transaction['balance_price'], 2) }}</td>
									</tr>
								@endforeach

								<tr>
									<td class="text-right" colspan="5"><strong>Balance Due</strong></td>
									<td class="text-right"><strong>£{{ number_format($balanceDue, 2) }}</strong></td>
								</tr>
							</tbody>
						</table>
					</div>

				@endif
			</div>
		</div>
	</div>
</div>
	<script>
		$(document).ready(function () {
			$('#range').change(function () {
				if ($(this).val() === "custom") {
					$('#customDateRange').removeClass('d-none');
				} else {
					$('#customDateRange').addClass('d-none');
				}
			});
			render_garage_statement();
		});


		function formatDate(dateStr) {
			let d = new Date(dateStr);
			let day = String(d.getDate()).padStart(2, '0');
			let month = String(d.getMonth() + 1).padStart(2, '0');
			let year = d.getFullYear();
			return `${day}-${month}-${year}`;
		}

		function render_garage_statement() {
			let range = $('#range').val();
			let fromDate, toDate;

			if (range === "custom") {
				fromDate = $('#custom_from').val();
				toDate = $('#custom_to').val();

				if (!fromDate || !toDate) {
					alert("Please select valid From and To dates.");
					return;
				}

				fromDate = formatDate(fromDate);
				toDate = formatDate(toDate);
			} else {
				let dates = JSON.parse(range);
				fromDate = dates[0];
				toDate = dates[1];
			}

			$.ajax({
				url: `garage/auth/statement`,
				type: "GET",
				data: { from: fromDate, to: toDate },
				success: function (response) {
					$('#summary_range').text(fromDate + " to " + toDate);
					$('#totalInvoiced').text(response.totalInvoiced.toFixed(2));
					$('#totalPaid').text(response.totalPaid.toFixed(2));
					$('#balanceDue').text(response.balanceDue.toFixed(2));

					let transactionsHtml = '';
					response.transactions.forEach(transaction => {
						transactionsHtml += `
							<tr>
								<td class="no-wrap">${transaction.date}</td>
								<td>${transaction.details}</td>
								<td>${transaction.type}</td>
								<td class="text-right">£${parseFloat(transaction.amount).toFixed(2)}</td>
								<td class="text-right">£${parseFloat(transaction.paid_price).toFixed(2)}</td>
								<td class="text-right">£${parseFloat(transaction.balance_price).toFixed(2)}</td>
							</tr>
						`;
					});

					transactionsHtml += `
						<tr>
							<td class="text-right" colspan="5"><strong>Balance Due</strong></td>
							<td class="text-right"><strong>£${response.balanceDue.toFixed(2)}</strong></td>
						</tr>
					`;

					$('#transactionsTable').html(transactionsHtml);
				},
				error: function (xhr) {
					let msg = "Unknown error.";
					if (xhr.responseJSON && xhr.responseJSON.error) {
						msg = xhr.responseJSON.error;
					}
					alert("Error fetching statement: " + msg);
				}
			});
		}
	</script>

	<style>
		.tox-statusbar__branding,
		.tox-promotion {
			display: none;
		}

		.thead-dark {
			background: #000;
			color: #fff;
		}
	</style>
@endsection