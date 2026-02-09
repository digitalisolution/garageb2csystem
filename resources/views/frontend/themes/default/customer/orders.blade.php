@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			@include('customer.menu')
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Orders</strong></h2>
				</div>
                        @if ($errors->any())
                            <ul class="alert alert-danger" style="list-style:none">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <!-- Notifications -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session()->has('message.level'))
                            <div class="alert alert-{{ session('message.level') }} alert-dismissible"
                                onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
                                <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
                                {!! session('message.content') !!}
                            </div>
                        @endif
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
										@if ($order->status === 'completed')
											<badge class="bg-success text-white rounded px-2 no-wrap">{{ strtoupper($order->status) }}</badge>
										@elseif ($order->status === 'cancelled')
											<badge class="bg-danger text-white rounded px-2 no-wrap">{{ strtoupper($order->status) }}</badge>
										@elseif ($order->status === 'processing')
											<badge class="bg-warning text-white rounded px-2 no-wrap">{{ strtoupper($order->status)}}</badge>
										@else
											<badge class="bg-primary text-white rounded px-2 no-wrap">{{strtoupper($order->status) }}</badge>
										@endif
									</td>
									<td>£{{ number_format($order->grandTotal, 2) }}</td>
									<td class="no-wrap">
										<a href="{{ route('customer.orders.view', $order->id) }}"
											class="btn btn-info btn-sm text-white" target="_blank">
											<i class="fa fa-eye"></i>
										</a>
										@if ($order->status === 'booked')
											<form action="{{ route('workshop.void', $order->id) }}" method="POST"
												style="display:inline;">
												@csrf
												<button type="submit" class="btn btn-danger btn-sm"
													onclick="return confirm('Are you sure you want to cancel this booking?');">
													<i class="fa fa-times"></i> Cancel
												</button>
											</form>
										@endif
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