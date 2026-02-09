@extends('samples')

@section('content')
		@php
	$role_id = Auth::user()->role_id;
		@endphp
		<div class="container-fluid">
			<div class="bg-white p-3">
				@include('AutoCare.garages.menu')
				<div class="short__item">
					<div class="bg-light p-2 text-center rounded border mb-4">
						<h5 class="m-0"><strong>Orders</strong></h5>
					</div>
					<div class="table-responsive">
						<table id="datable_1" class="table table-striped border text-center">
							<thead class="thead-dark">
								<tr>
									<th class="no-wrap">Job Number</th>
									<th>VRM</th>
									<th class="no-wrap">Booking Date</th>
									<th class="no-wrap">Booking Time</th>
									<th class="no-wrap">Order Type</th>
									<th>Status</th>
									<th>Total</th>
									<th class="text-right">Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($workshops as $value)
									<tr>
										<td class="no-wrap">JOB-{{ $value->id }}</td>
										<td class="text-uppercase">{{ $value['vehicle_reg_number'] }}</td>
										<td>{{ \Carbon\Carbon::parse($value['due_in'])->format('d-m-Y') }}</td>
										<td>{{ \Carbon\Carbon::parse($value['due_in'])->format('H:i') }} -
											{{ \Carbon\Carbon::parse($value['due_out'])->format('H:i') }}
										</td>
										<td class="no-wrap">
											{{ optional($value['items'][0] ?? null)['fitting_type'] ? ucwords(str_replace('_', ' ', $value['items'][0]['fitting_type'])) : 'N/A' }}
										</td>

										<td>
											@if ($value['status'] === 'Work Complete')
												<badge class="bg-success text-white rounded px-2 no-wrap">{{ $value['status'] }}</badge>
											@elseif ($value['status'] === 'Collection')
												<badge class="bg-primary text-white rounded px-2 no-wrap">{{ $value['status'] }}</badge>
											@else
												<badge class="bg-warning text-white rounded px-2 no-wrap">{{ $value['status'] }}</badge>
											@endif
										</td>
										<td>£{{ number_format($value['grandTotal'], 2) }}</td>
										<td class="no-wrap text-right">
											@if ($value['is_converted_to_invoice'] == 1)
												<a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $value->id }}" class="btn btn-warning btn-sm">
													<i class="fa fa-pencil" aria-hidden="true"></i> Update Invoice
												</a>
												<a target="_blank" href="{{ url('/') }}/AutoCare/workshop/invoice/{{ $value->id }}"
													class="btn btn-primary btn-sm">
													<i class="fa fa-eye"></i>
												</a>
												@if ($role_id == 1)
													<button type="button" class="btn btn-info btn-sm" data-toggle="modal"
														data-target="#emailModal{{ $value->id }}">
														<i class="fa fa-envelope"></i> Email Invoice
													</button>
													@include('AutoCare.workshop.invoice-email-modal', ['invoiceId' => $value->id])
													<a href="{{ route('invoice.preview', $value->id) }}" target="_blank" class="btn btn-info btn-sm">
														<i class="fa fa-eye"></i> Preview PDF
													</a>
												@endif
											@else
												<a href="{{ url('/') }}/AutoCare/workshop/addinvoice/{{ $value->id }}" class="btn btn-primary btn-sm">Convert
													to
													Invoice
												</a>
											@endif
											<a data-toggle="modal" id="{{ $value->id }}" data-target="#workshopDiscount"
												class="btn btn-success openDiscountModelForWorkshop btn-sm">Discount</a>

											<a data-toggle="modal" id="{{ $value->id }}" data-target="#workshopPayment"
												class="btn btn-success openPayentModelForWorkshop btn-sm"><i class="fa fa-money" aria-hidden="true"></i></a>

											<a target="blank" href="{{ url('/') }}/AutoCare/workshop/view/{{ $value->id }}"
												class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>

											@if ($value['is_workshop'] == 1)
												<a target="blank" href="{{ url('/') }}/AutoCare/workshop/payment_history/{{ $value->id }}"
													class="btn btn-danger btn-sm" title="Payment History">
													<i class="fa fa-eye"></i>
												</a>
												<a href="{{ url('/') }}/AutoCare/workshop/add/{{ $value->id }}" class="btn btn-success btn-sm"><i
														class="fa fa-edit"></i></a>
											@else
												<a data-toggle="modal" id="{{ $value->id }}" data-target="#myModal1"
													class="btn btn-success openPayentModel btn-sm"><i class="fa fa-undo" aria-hidden="true"></i></a>
												<a href="{{ url('/') }}/AutoCare/sale/edit/{{ $value->id }}" class="btn btn-success btn-sm"><i
														class="fa fa-edit"></i></a>
											@endif
											@if ($role_id == 1)
												<a href="{{ url('/') }}/AutoCare/workshop/trash/{{ $value->id }} " class="btn btn-danger btn-sm"
													onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-remove"></i></a>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

					</div>
					
				</div>
			</div>
		</div>
@endsection