@extends('samples')

@section('content')
		<div class="container-fluid">
			<div class="bg-white p-3">
			@include('AutoCare.customer.menu')
			<div class="short__item">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Invoices</strong></h2>
					<a class="text-link" href="{{ route('AutoCare.customer.statement', ['id' => $customer->id]) }}">View
						Account Statement</a>
				</div>
				<div class="invoice_bank">
					<div class="item">
						Unpaid <span class="bg-red">{{ $unpaidCount }}/{{ $invoices->count() }}</span>
					</div>
					<div class="item">
						Paid <span class="bg-green">{{ $paidCount }}/{{ $invoices->count() }}</span>
					</div>
					<div class="item">
						Overdue <span class="bg-orange">{{ $overdueCount }}/{{ $invoices->count() }}</span>
					</div>
					<div class="item">
						Partially Paid <span class="bg-blue">{{ $partiallyPaidCount }}/{{ $invoices->count() }}</span>
					</div>
				</div>
				<div class="table-responsive">
					<table id="datable_1" class="table table-striped binvoice text-center">
						<thead class="thead-dark">
							<tr>
								<th>Invoice</th>
								<th>Date</th>
								<th class="no-wrap">Due Date</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($invoices as $invoice)
								<tr>
									<td>INV-{{ $invoice->workshop_id }}</td>
									<td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</td>
									<td>{{ \Carbon\Carbon::parse($invoice->due_out)->format('d-m-Y') }}</td>
									<td>£{{ number_format($invoice->grandTotal, 2) }}</td>
									<td>
										@if ($invoice->payment_status === 0)
											<badge class="bg-danger text-white rounded px-2 no-wrap">Unpaid</badge>
										@elseif ($invoice->payment_status === 1)
											<badge class="bg-success text-white rounded px-2 no-wrap">Paid</badge>
										@elseif ($invoice->payment_status === 2)
											<badge class="bg-warning text-white rounded px-2 no-wrap">Overdue</badge>
										@elseif ($invoice->payment_status === 3)
											<badge class="bg-info text-white rounded px-2 no-wrap">Partially-Paid</badge>
										@else
											<badge class="bg-info text-white rounded px-2 no-wrap">Unpaid</badge>
										@endif
									</td>
									<td><a href="{{ url('/') }}/AutoCare/workshop/invoice/{{ $invoice->workshop_id }}"
											target="_blank" class="btn btn-info btn-sm text-white"><i class="fa fa-eye"></i></a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5">No invoices found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection