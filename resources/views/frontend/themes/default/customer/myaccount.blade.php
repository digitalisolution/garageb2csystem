@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			<!-- Customer Menu -->
			@include('customer.menu')
			<!-- Profile Section -->
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Profile</strong></h2>
				</div>
				<form action="{{ route('customer.update-profile') }}" method="POST">
					@csrf
					<div class="row">
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>First Name</label>
								<input type="text" name="customer_name"
									value="{{ old('customer_name', Auth::guard('customer')->user()->customer_name) }}"
									required minlength="2" maxlength="50">
								@error('customer_name')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Last Name</label>
								<input type="text" name="customer_last_name"
									value="{{ old('customer_last_name', Auth::guard('customer')->user()->customer_last_name) }}"
									required minlength="2" maxlength="50">
								@error('customer_last_name')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Email Address</label>
								<input type="email" name="customer_email"
									value="{{ old('customer_email', Auth::guard('customer')->user()->customer_email) }}"
									required>
								@error('customer_email')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Telephone</label>
								<input type="text" name="customer_contact_number"
									value="{{ old('customer_contact_number', Auth::guard('customer')->user()->customer_contact_number) }}"
									required minlength="10" maxlength="15">
								@error('customer_contact_number')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Alternate Telephone</label>
								<input type="text" name="customer_alt_number"
									value="{{ old('customer_alt_number', Auth::guard('customer')->user()->customer_alt_number) }}"
									minlength="10" maxlength="15">
								@error('customer_alt_number')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Company Name</label>
								<input type="text" name="company_name"
									value="{{ old('company_name', Auth::guard('customer')->user()->company_name) }}"
									maxlength="100">
								@error('company_name')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Website</label>
								<input type="text" name="company_website"
									value="{{ old('company_website', Auth::guard('customer')->user()->company_website) }}"
									maxlength="100">
								@error('company_website')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Profile</button></div>
				</form>
			</div>

			<!-- Password Update Section -->
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Update Password</strong></h2>
				</div>
				<form action="{{ route('customer.update-password') }}" method="POST">
					@csrf
					<div class="row">
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Current Password</label>
								<input type="password" name="current_password" required minlength="8">
								@error('current_password')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>New Password</label>
								<input type="password" name="new_password" required minlength="8">
								@error('new_password')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Confirm New Password</label>
								<input type="password" name="new_password_confirmation" required minlength="8">
							</div>
						</div>
					</div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Password</button></div>
				</form>
			</div>

			<!-- Billing and Shipping Section -->
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Billing and Shipping</strong></h2>
				</div>
				<!-- Billing Address Form -->
				<form action="{{ route('customer.update-billing-address') }}" method="POST">
					@csrf
					<h3><strong>Billing Address</strong></h3>
					<div class="row">
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Street</label>
								<input type="text" name="billing_address_street"
									value="{{ old('billing_address_street', Auth::guard('customer')->user()->billing_address_street) }}"
									required minlength="3" maxlength="100">
								@error('billing_address_street')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>City</label>
								<input type="text" name="billing_address_city"
									value="{{ old('billing_address_city', Auth::guard('customer')->user()->billing_address_city) }}"
									required minlength="3" maxlength="50">
								@error('billing_address_city')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Postcode</label>
								<input type="text" name="billing_address_postcode"
									value="{{ old('billing_address_postcode', Auth::guard('customer')->user()->billing_address_postcode) }}"
									required minlength="3" maxlength="10">
								@error('billing_address_postcode')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>County</label>
								<select class="form-control" name="billing_address_county" required>
									<option value="">Select</option>
									@foreach ($counties as $county)
										<option value="{{ $county->zone_id }}" {{ old('billing_address_county', Auth::guard('customer')->user()->billing_address_county) == $county->zone_id ? 'selected' : '' }}>
											{{ $county->name }}
										</option>
									@endforeach
								</select>
								@error('billing_address_county')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Country</label>
								<select class="form-control" name="billing_address_country" required>
									<option value="">Select</option>
									<option value="1" {{ old('billing_address_country', Auth::guard('customer')->user()->billing_address_country) == '1' ? 'selected' : '' }}>United Kingdom
									</option>
								</select>
								@error('billing_address_country')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Billing Address</button></div>
				</form>

				<!-- Shipping Address Form -->
				<form action="{{ route('customer.update-shipping-address') }}" method="POST" class="mt-4">
					@csrf
					<h3><strong>Shipping Address</strong></h3>
					<div class="form-group mt-3">
						<div class="sidebar-widget-list-left">
							<label class="remember-flex">
								<input type="checkbox" id="same_as_billing"> Same as Billing Address
							</label>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Street</label>
								<input type="text" name="shipping_address_street" id="shipping_address_street"
									value="{{ old('shipping_address_street', Auth::guard('customer')->user()->shipping_address_street) }}"
									required minlength="3">
								@error('shipping_address_street')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>City</label>
								<input type="text" name="shipping_address_city" id="shipping_address_city"
									value="{{ old('shipping_address_city', Auth::guard('customer')->user()->shipping_address_city) }}"
									required minlength="3">
								@error('shipping_address_city')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Postcode</label>
								<input type="text" name="shipping_address_postcode" id="shipping_address_postcode"
									value="{{ old('shipping_address_postcode', Auth::guard('customer')->user()->shipping_address_postcode) }}"
									required minlength="3">
								@error('shipping_address_postcode')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>County</label>
								<select class="form-control" name="shipping_address_county" id="shipping_address_county">
									<option value="">Select</option>
									@foreach ($counties as $county)
										<option value="{{ $county->zone_id }}" {{ old('shipping_address_county', Auth::guard('customer')->user()->shipping_address_county) == $county->zone_id ? 'selected' : '' }}>
											{{ $county->name }}
										</option>
									@endforeach
								</select>
								@error('shipping_address_county')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-12">
							<div class="form-group">
								<label>Country</label>
								<select class="form-control" name="shipping_address_country" id="shipping_address_country">
									<option value="">Select</option>
									<option value="1" {{ old('shipping_address_country', Auth::guard('customer')->user()->shipping_address_country) == '1' ? 'selected' : '' }}>United
										Kingdom</option>
								</select>
								@error('shipping_address_country')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Shipping Address</button></div>
				</form>
			</div>
		</div>
	</div>
	<script>
		document.getElementById('same_as_billing').addEventListener('change', function () {
			if (this.checked) {
				// Copy billing address fields to shipping address fields
				document.getElementById('shipping_address_street').value = document.querySelector('[name="billing_address_street"]').value;
				document.getElementById('shipping_address_city').value = document.querySelector('[name="billing_address_city"]').value;
				document.getElementById('shipping_address_postcode').value = document.querySelector('[name="billing_address_postcode"]').value;
				document.getElementById('shipping_address_county').value = document.querySelector('[name="billing_address_county"]').value;
				document.getElementById('shipping_address_country').value = document.querySelector('[name="billing_address_country"]').value;
			} else {
				// Restore the original values from the database
				document.getElementById('shipping_address_street').value = "{{ old('shipping_address_street', Auth::guard('customer')->user()->shipping_address_street) }}";
				document.getElementById('shipping_address_city').value = "{{ old('shipping_address_city', Auth::guard('customer')->user()->shipping_address_city) }}";
				document.getElementById('shipping_address_postcode').value = "{{ old('shipping_address_postcode', Auth::guard('customer')->user()->shipping_address_postcode) }}";
				document.getElementById('shipping_address_county').value = "{{ old('shipping_address_county', Auth::guard('customer')->user()->shipping_address_county) }}";
				document.getElementById('shipping_address_country').value = "{{ old('shipping_address_country', Auth::guard('customer')->user()->shipping_address_country) }}";
			}
		});

	</script>
@endsection