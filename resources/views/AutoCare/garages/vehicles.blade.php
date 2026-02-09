@extends('samples')

@section('content')
	<div class="container-fluid">
		<div class="bg-white p-3">
			@include('AutoCare.customer.menu')
			<div class="short__item">
				<div class="bg-light p-2 text-center rounded border mb-4">
					<h6 class="m-0"><strong>Vehicle List</strong></h6>
				</div>
				<div class="text-right mb-4">
					<a href="{{ route('AutoCare.customer.vehicles.create', ['id' => $customer->id]) }}"
						class="btn btn-primary">Add Vehicle</a>
				</div>
				<div class="table-responsive">
					<table class="table table-striped border">
						<thead class="thead-dark">
							<tr>
								<th class="no-wrap">Vehicle Category</th>
								<th>VRM</th>
								<th>Make</th>
								<th>Model</th>
								<th class="no-wrap">Registered Date</th>
								<th class="no-wrap">MOT Expiry Date</th>
								<!-- <th class="no-wrap">Total Jobs</th> -->
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($vehicles as $vehicle)
								<tr>
									<td class="no-wrap">Car</td>
									<td class="text-uppercase">{{ $vehicle->vehicle_reg_number }}</td>
									<td>{{ $vehicle->vehicle_make }}</td>
									<td>{{ $vehicle->vehicle_model }}</td>
									<td>{{ $vehicle->vehicle_first_registered }}</td>
									<td>{{ $vehicle->vehicle_mot_expiry_date }}</td>
									<!-- <td>
												<a href="javascript:void(0);" class="btn btn-info btn-sm text-white"
													data-bs-toggle="modal" data-bs-target="#exampleModal">
													<i class="fa fa-eye"></i>
												</a>
											</td> -->
									<td class="no-wrap">
										<a href="{{ route('AutoCare.customer.vehicles.edit', ['id' => $customer->id, 'vehicleId' => $vehicle->id]) }}"
											class="btn btn-warning btn-sm text-white">
											<i class="fa fa-edit"></i>
										</a>
										<form
											action="{{ route('AutoCare.customer.vehicles.delete', ['id' => $customer->id, 'vehicleId' => $vehicle->id]) }}"
											method="POST" style="display: inline;">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-danger btn-sm">
												<i class="fa fa-remove"></i>
											</button>
										</form>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9">No vehicles found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					<div class="d-flex justify-content-center mt-4">
						{{ $vehicles->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


<!-- Start View job Modal 
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-vertical-center" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="m-0"><span class="text-uppercase">SH12NZA</span> Job History</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-3">
				<div class="customer_job_history">
					<div class="item">
						<div class="d-flex align-items-center">
							<h6><strong>Feb 10, 2025, 6:00AM</strong></h6>
							<badge class="bg-green no-wrap px-1 py-0 rounded ml-auto">Work Complete</badge>
						</div>
						<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
						<h6>Total Amount: £191.30</h6>
						<div class="job_badges">
							<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View
								Job-GEC001</a>
						</div>
					</div>
					<div class="item">
						<div class="d-flex align-items-center">
							<h6><strong>Feb 10, 2025, 6:00AM</strong></h6>
							<badge class="bg-orange no-wrap px-1 py-0 rounded ml-auto">Collection</badge>
						</div>
						<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
						<h6>Total Amount: £191.30</h6>
						<div class="job_badges">
							<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View
								Job-GEC001</a>
						</div>
					</div>
					<div class="item">
						<div class="d-flex align-items-center">
							<h6><strong>Feb 10, 2025, 6:00AM</strong></h6>
							<badge class="bg-orange no-wrap px-1 py-0 rounded ml-auto">Collection</badge>
						</div>
						<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
						<h6>Total Amount: £191.30</h6>
						<div class="job_badges">
							<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View
								Job-GEC001</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 End View job Modal -->