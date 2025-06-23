@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			@include('customer.menu')
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Vehicle List</strong></h2>
				</div>
				<div class="text-right mb-4">
						<a href="{{ route('customer.vehicles.create') }}" class="btn btn-theme-select">Add Vehicle</a>
					</div>
				<div class="table-responsive">
					<table class="table table-striped border">
						<thead class="bg-dark text-white">
							<tr>
								<th class="no-wrap">Vehicle Category</th>
								<th>VRM</th>
								<th>Make</th>
								<th>Model</th>
								<th class="no-wrap">MOT Expiry Date</th>
								<th class="no-wrap">Total Jobs</th>
								<th class="no-wrap">Accepted On</th>
								<th class="no-wrap">Comment</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($vehicles as $vehicle)
								<tr>
									<td class="no-wrap">Car</td>
									<td class="text-uppercase">{{ $vehicle->vehicle_reg_number }}</td>
									<td>{{ $vehicle->make }}</td>
									<td>{{ $vehicle->model }}</td>
									<td>{{ $vehicle->mot_expiry_date }}</td>
									<td>
										<a href="javascript:void(0);" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#exampleModal">
											<i class="fa fa-eye"></i>
										</a>
									</td>
									<td>{{ $vehicle->accepted_on }}</td>
									<td>{{ $vehicle->comment }}</td>
									<td class="no-wrap">
										<a href="{{ route('customer.vehicles.edit', $vehicle->id) }}"
											class="btn btn-warning btn-sm text-white">
											<i class="fa fa-edit"></i>
										</a>
										<form action="{{ route('customer.vehicles.delete', $vehicle->id) }}" method="POST"
											style="display: inline;">
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
<!-- Start View job Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-vertical-center" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="m-0"><span class="text-uppercase">SH12NZA</span> Job History</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
            	<div class="customer_job_history">
            		<div class="item">
            			<div class="d-flex align-items-center">
            				<h5><strong>Feb 10, 2025, 6:00AM</strong></h5>
            				<badge class="bg-green no-wrap px-1 py-0 rounded ml-auto">Work Complete</badge>
            			</div>
	            		<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
	            		<h4>Total Amount: £191.30</h4>
	            		<div class="job_badges">
						<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View Job-GEC001</a>
						</div>
					</div>
					<div class="item">
            			<div class="d-flex align-items-center">
            				<h5><strong>Feb 10, 2025, 6:00AM</strong></h5>
            				<badge class="bg-orange no-wrap px-1 py-0 rounded ml-auto">Collection</badge>
            			</div>
	            		<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
	            		<h4>Total Amount: £191.30</h4>
	            		<div class="job_badges">
						<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View Job-GEC001</a>
						</div>
					</div>
					<div class="item">
            			<div class="d-flex align-items-center">
            				<h5><strong>Feb 10, 2025, 6:00AM</strong></h5>
            				<badge class="bg-orange no-wrap px-1 py-0 rounded ml-auto">Collection</badge>
            			</div>
	            		<p><i class="fa fa-gear"></i> Wheel Balancing,Tyre</p>
	            		<h4>Total Amount: £191.30</h4>
	            		<div class="job_badges">
						<a href="javascript:void(0);" class="bg-red px-2 py-0 rounded text-white">View Job-GEC001</a>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<!-- End View job Modal -->