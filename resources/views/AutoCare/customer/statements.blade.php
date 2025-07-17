@extends('samples')
@section('content')
	@if($transactions->isEmpty())
		<p>No transactions found for this customer.</p>
	@else
		<input type="hidden" id="loadStatement" data-customer-id="{{ $customer->id }}"></input>

		<div class="container-fluid">
			<div class="bg-white p-3">
				@include('AutoCare.customer.menu')
				<div class="short__item">
					<div class="statement_bank">
						<div class="item">
							<div class="form-group">
								<label for="range" class="mb-1"><strong>Select Time Period</strong></label>
								<select class="selectpicker form-control" name="range" id="range" data-width="100%"
									onchange="render_customer_statement();">
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
										onchange="render_customer_statement();">
								</div>
								<div>
									<label for="custom_to">To:</label>
									<input type="date" id="custom_to" class="form-control"
										onchange="render_customer_statement();">
								</div>
							</div>
						</div>
						<div class="print-download ml-auto">
							<!-- <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print &amp;
																			Download</a> -->
							<a href="{{ route('customer.statement.preview', $customer->id) }}" target="_blank"
								class="btn btn-primary">Preview</a>
							<a href="{{ route('customer.statement.download', $customer->id) }}"
								class="btn btn-success">Download</a>

							<!-- Modal Trigger -->
							<button type="button" class="btn btn-info" data-bs-toggle="modal"
								data-bs-target="#sendStatementModal">
								Send to Email
							</button>

						</div>

					</div>

					<!-- Customer Details -->
					<div class="border p-4">
						<div class="pb-4">
							<div class="row align-items-end">
								<div class="col-lg-5 col-md-5 col-sm-6 col-12">
									<address>
										<h4 class="text-4 mb-1">{{$garage->garage_name}}</h4>
										Registration No: {{$garage->company_number}}<br>
										VAT Number: {{$garage->vat_number}}<br>
										Telephone: {{$garage->phone}}<br>
										Email: {{$garage->email}}<br>
										{{$garage->street}},{{$garage->city}},{{$garage->zone}},{{$garage->country}}
									</address>
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
								<div class="col-lg-5 col-md-5 col-sm-6 col-12 offset-md-2 text-sm-end">
									<address class="text-sm-end">
															 @if(!empty($garage->logo))
                        <img id="logo" src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Logo" width="auto" height="70" /><br>
                    @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                        <img id="logo" src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}"
                            title="Garage Name" alt="Logo" width="auto" height="70" /><br>
                    @else

                        <img id="logo" src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}" title="Garage Name"
                             alt="Logo" width="auto" height="70" /><br>
                    @endif
									</address>
								</div>

							</div>
						</div>
						<div class="text-center">
							<h2 class="m-0 text-uppercase"><strong>Statement</strong></h2>
						</div>
						<div class="pl-5 pt-4">
							<div class="row align-items-end">
								<div class="col-lg-5 col-md-5 col-sm-6 col-12">
									<address>
										<h4 class="text-4 mb-1">{{ $customer->customer_name }}</h4>
										Telephone: {{ $customer->customer_contact_number }}<br>
										Email: {{ $customer->customer_email }}<br>
										Address: {{ $customer->customer_address }}
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
					</div>

					<!-- Account Summary -->
					<div class="text-center my-3">
						<h4 class="ac_summary">Account Summary:
							<span id="summary_range">{{ now()->startOfMonth()->format('d/m/Y') }} to
								{{ now()->endOfMonth()->format('d/m/Y') }}</span>
						</h4>
					</div>

					<!-- Account Summary Table -->
					<div class="table-responsive">
						<table class="table table-striped">
							<thead class="thead-dark">
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
						<table class="table table-striped">
							<thead class="thead-dark">
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

				</div>
			</div>
		</div>
	@endif

	<!-- Send Statement Email Modal -->
	<div class="modal fade" id="sendStatementModal" tabindex="-1" aria-labelledby="sendStatementModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-md">
			<form action="{{ route('customer.statement.email') }}" method="POST">
				@csrf
				<input type="hidden" name="customer_id" value="{{ $customer->id }}">

				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="sendStatementModalLabel">Send Statement</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="mb-3">
							<label for="email_to" class="form-label">To</label>
							<input type="email" name="email_to" value="{{ $customer->customer_email }}" class="form-control"
								required>
						</div>
						<div class="mb-3">
							<label for="email_cc" class="form-label">CC (Optional)</label>
							<input type="email" name="email_cc" class="form-control">
						</div>
						<div class="mb-3 form-check">
							<input type="checkbox" name="attach_pdf" value="1" checked class="form-check-input"
								id="attachPdfCheck">
							<label class="form-check-label pl-0" for="attachPdfCheck">Attach PDF</label>
						</div>
						<div class="form-group">
							<label for="email_body">Body:</label>
							<textarea id="email_body" name="email_body">{!! getStatementEmailBody($customer) !!}</textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Send Email</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- JavaScript for Functionality -->
	<script>
		$(document).ready(function () {
			// Handle time period change
			$('#range').change(function () {
				if ($(this).val() === "custom") {
					$('#customDateRange').removeClass('d-none'); // Show custom date fields
				} else {
					$('#customDateRange').addClass('d-none'); // Hide custom date fields
				}
			});

			// Print Statement
			$('#printStatement').click(function () {
				window.print();
			});

			// Download PDF
			$('#downloadPDF').click(function () {
				window.location.href = "{{ route('AutoCare.customer.statement.pdf') }}";
			});

			// Send Email
			$('#sendEmail').click(function () {
				$.ajax({
					url: "{{ route('AutoCare.customer.statement.email') }}",
					type: "POST",
					data: { _token: "{{ csrf_token() }}" },
					success: function (response) {
						alert('Statement sent to email successfully.');
					},
					error: function () {
						alert('Failed to send email.');
					}
				});
			});
		});

		function render_customer_statement() {
			let range = $('#range').val();
			let fromDate, toDate;

			if (range === "custom") {
				fromDate = $('#custom_from').val();
				toDate = $('#custom_to').val();

				// Validate custom dates
				if (!fromDate || !toDate) {
					alert("Please select valid From and To dates.");
					return;
				}
			} else {
				let dates = JSON.parse(range);
				fromDate = dates[0];
				toDate = dates[1];
			}

			console.log("Fetching customer statement from:", fromDate, "to", toDate);

			let customerId = $('#loadStatement').data('customer-id'); // Fetch from button
			$.ajax({
				url: `/AutoCare/customer/details/${customerId}/statements`,
				type: "GET",
				data: { from: fromDate, to: toDate },
				success: function (response) {
					console.log(response);
					$('#summary_range').text(fromDate + " to " + toDate);
					$('#totalInvoiced').text(response.totalInvoiced.toFixed(2));
					$('#totalPaid').text(response.totalPaid.toFixed(2));
					$('#balanceDue').text(response.balanceDue.toFixed(2));

					// Dynamically populate the transactions table
					let transactionsHtml = '';
					response.transactionsHtml.forEach(transaction => {
						transactionsHtml += `
																		<tr>
																			<td class="no-wrap">${transaction.date}</td>
																			<td>${transaction.details}</td>
																			<td>${transaction.type}</td>
																			<td class="text-right">£${transaction.amount.toFixed(2)}</td>
																			<td class="text-right">£${transaction.paid_price.toFixed(2)}</td>
																			<td class="text-right">£${transaction.balance_price.toFixed(2)}</td>
																		</tr>
																	`;
					});

					// Add the balance due row
					transactionsHtml += `
																	<tr>
																		<td class="text-right" colspan="5"><strong>Balance Due</strong></td>
																		<td class="text-right"><strong>£${response.balanceDue.toFixed(2)}</strong></td>
																	</tr>
																`;

					$('#transactionsTable').html(transactionsHtml);
				},
				error: function (xhr) {
					alert("Error fetching statement: " + xhr.responseJSON?.error || "Unknown error.");
				}
			});
		}
	</script>
	<style>
		.tox-statusbar__branding,
		.tox-promotion {
			display: none;
		}
	</style>
@endsection