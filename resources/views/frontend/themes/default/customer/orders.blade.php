@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			@include('customer.menu')
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Orders</strong></h2>
				</div>
				<div class="table-responsive">
					<table class="table table-striped border text-center">
						<thead class="bg-dark text-white">
							<tr>
								<th class="no-wrap">Job Number</th>
								<th>VRM</th>
								<th class="no-wrap">Booking Date</th>
								<th class="no-wrap">Booking Time</th>
								<th class="no-wrap">Order Type</th>
								<th>Status</th>
								<th>Total</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($workshops as $order)
								<tr>
									<td class="no-wrap">JOB-{{ $order->id }}</td>
									<td class="text-uppercase">{{ $order->vehicle_reg_number }}</td>
									<td>{{ \Carbon\Carbon::parse($order->due_in)->format('d-m-Y') }}</td>
									<td>{{ \Carbon\Carbon::parse($order->due_in)->format('H:i') }} -
										{{ \Carbon\Carbon::parse($order->due_out)->format('H:i') }}
									</td>
									<td class="no-wrap">
										{{ optional($order->items->first())->fitting_type ? ucwords(str_replace('_', ' ', $order->items->first()->fitting_type)) : 'N/A' }}
									</td>

									<td>
										@if ($order->status === 'Work Complete')
											<badge class="bg-success text-white rounded px-2 no-wrap">{{ $order->status }}</badge>
										@elseif ($order->status === 'Collection')
											<badge class="bg-primary text-white rounded px-2 no-wrap">{{ $order->status }}</badge>
										@else
											<badge class="bg-warning text-white rounded px-2 no-wrap">{{ $order->status }}</badge>
										@endif
									</td>
									<td>£{{ number_format($order->grandTotal, 2) }}</td>
									<td class="no-wrap">
										<a href="{{ route('customer.orders.view', $order->id) }}"
											class="btn btn-info btn-sm text-white" target="_blank">
											<i class="fa fa-eye"></i>
										</a>
										<button type="submit" class="btn btn-danger btn-sm">Cancel</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					
				</div>
				<div class="d-flex justify-content-center mt-2">
						{{ $workshops->links() }}
					</div>
			</div>
		</div>
	</div>
@endsection