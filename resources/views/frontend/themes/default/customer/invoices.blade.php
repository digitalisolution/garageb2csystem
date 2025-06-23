@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			@include('customer.menu')
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Invoices</strong></h2>
					<a class="text-link" href="{{ route('customer.statement') }}">View Account Statement</a>
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
					<table class="table table-striped binvoice text-center">
						<thead class="bg-dark text-white">
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
										@if ($invoice->status === 'Unpaid')
											<badge class="bg-danger text-white rounded px-2 no-wrap">{{ $invoice->status }}</badge>
										@elseif ($invoice->status === 'Paid')
											<badge class="bg-success text-white rounded px-2 no-wrap">{{ $invoice->status }}</badge>
										@elseif ($invoice->status === 'Overdue')
											<badge class="bg-warning text-white rounded px-2 no-wrap">{{ $invoice->status }}</badge>
										@elseif ($invoice->status === 'Partially Paid')
											<badge class="bg-info text-white rounded px-2 no-wrap">{{ $invoice->status }}</badge>
										@else
											<badge class="bg-info text-white rounded px-2 no-wrap">{{ $invoice->status }}</badge>
										@endif
									</td>
									<td><a href="{{ route('customer.invoice.view', $invoice->workshop_id) }}"
											class="btn btn-info btn-sm text-white" target="_blank" ><i class="fa fa-eye"></i></a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5">No invoices found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					<div class="d-flex justify-content-center mt-4">
						{{ $invoices->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection