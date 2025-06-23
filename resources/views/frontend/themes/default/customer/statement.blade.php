@extends('layouts.app')
@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			@include('customer.menu')
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Statement</strong></h2>
				</div>
				<div class="statement_bank">
					<div class="item">
						<select class="form-control">
							<option>Today</option>
							<option>This Week</option>
							<option selected="">This Month</option>
							<option>Last Month</option>
							<option>This Year</option>
							<option>Last Year</option>
							<option>Period</option>
						</select>
					</div>
					<div class="print-download ml-auto"> <a href="javascript:window.print()"
							class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print &amp;
							Download</a></div>
				</div>
				<div class="border px-4 py-4">
					<div class="row">
						<div class="col-lg-5 col-md-5 col-12">
							<address>
								To
								<h4 class="text-4 mb-1">North London Mobile Tyre Fitting</h4>
								Registration No: <br>
								VAT Number: <br>
								Telephone: 0208 068 3237<br>
								Email: byron@fastfitservicecentre.com<br>
								Unit 1, Moniton Trading Estate, West Ham Lane, HampshireBasingstokeRG22 5EEUK
							</address>
						</div>
						<div class="col-lg-5 col-md-5 col-12 offset-2 text-sm-end">
							<address class="text-sm-end">
								<h4 class="text-4 mb-1">Demo GEC Garage</h4>
								Registration No: <br>
								VAT Number: <br>
								Telephone: 0208 068 3237<br>
								Email: byron@fastfitservicecentre.com<br>
								Unit 1, Moniton Trading Estate, West Ham Lane, HampshireBasingstokeRG22 5EEUK
							</address>
						</div>
					</div>
				</div>
				<div class="text-center my-3">
					<h4>Account Summary: 01/03/2025 to 31/03/2025</h4>
				</div>
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
								<td>£0.00</td>
								<td class="text-right">£0.00</td>
								<td class="text-right">£0.00</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="table-responsive">
					<table class="table table-striped border">
						<thead class="bg-dark text-white">
							<tr>
								<th>Date</th>
								<th>Details</th>
								<th>Payments</th>
								<th class="text-right">Amount</th>
								<th class="text-right">Balance</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="no-wrap">01/03/2025</td>
								<td>Beginning Balance</td>
								<td>--</td>
								<td class="text-right">0.00</td>
								<td class="text-right">0.00</td>
							</tr>
							<tr>
								<td class="text-right" colspan="4"><strong>Balance Due</strong></td>
								<td class="text-right"><strong>£0.00</strong></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection