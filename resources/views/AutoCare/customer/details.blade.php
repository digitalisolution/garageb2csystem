@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <!-- Customer Menu -->
            @include('AutoCare.customer.menu')
            <!-- Profile Section -->
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
        
            <div class="mb-5">
                <div class="bg-light p-2 text-center border rounded mb-4">
                    <h5 class="m-0"><strong>Profile</strong></h5>
                </div>
                <form action="{{ route('AutoCare.customer.update-profile', ['id' => $customer->id]) }}" method="POST">
                    @csrf
    <!-- Customer Type -->
    <div class="mb-4">
        <label for="customerType">Customer Type</label>
        <select id="customerType" name="customer_type" class="form-control" required>
            <option value="">-- Select --</option>
            <option value="individual" {{ old('customer_type', $customer->customer_type ?? '') == 'individual' ? 'selected' : '' }}>
                Individual
            </option>
            <option value="trade" {{ old('customer_type', $customer->customer_type ?? '') == 'trade' ? 'selected' : '' }}>
                Trade
            </option>
        </select>
    </div>

    <!-- Conditional Fields -->
    <div id="individualFields">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="customer_name"
                        value="{{ old('customer_name', $customer->customer_name ?? '') }}" required minlength="2"
                        maxlength="50">
                    @error('customer_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="customer_last_name"
                        value="{{ old('customer_last_name', $customer->customer_last_name ?? '') }}"
                        minlength="2" maxlength="50">
                    @error('customer_last_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Email Address</label>
                    <input class="form-control" type="email" name="customer_email"
                        value="{{ old('customer_email', $customer->customer_email) }}" required>
                    @error('customer_email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Telephone</label>
                    <input class="form-control" type="text" name="customer_contact_number"
                        value="{{ old('customer_contact_number', $customer->customer_contact_number) }}" required minlength="10" maxlength="15">
                    @error('customer_contact_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Alternate Telephone</label>
                    <input class="form-control" type="text" name="customer_alt_number"
                        value="{{ old('customer_alt_number', $customer->customer_alt_number) }}" minlength="10" maxlength="15">
                    @error('customer_alt_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div id="tradeFields" style="display: none;">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Company Name</label>
                    <input class="form-control" type="text" name="company_name"
                        value="{{ old('company_name', $customer->company_name ?? '') }}" required>
                    @error('company_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>VAT Number</label>
                    <input class="form-control" type="text" name="vat_number"
                        value="{{ old('vat_number', $customer->vat_number ?? '') }}">
                    @error('vat_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Company Registration Number</label>
                    <input class="form-control" type="text" name="company_registration_number"
                        value="{{ old('company_registration_number', $customer->company_registration_number ?? '') }}">
                    @error('company_registration_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Website</label>
                    <input class="form-control" type="url" name="company_website"
                        value="{{ old('company_website', $customer->company_website ?? '') }}" placeholder="https://example.com"> 
                    @error('company_website')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Contact Name</label>
                    <input class="form-control" type="text" name="contact_person"
                        value="{{ old('contact_person', $customer->contact_person ?? '') }}" required>
                    @error('contact_person')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-12">
                <div class="form-group">
                    <label>Contact Email</label>
                    <input class="form-control" type="email" name="contact_email"
                        value="{{ old('contact_email', $customer->contact_email ?? '') }}" required>
                    @error('contact_email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
                    <div class="text-center mt-3"><button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>

            <!-- Password Update Section -->
            <div class="mb-5">
                <div class="bg-light p-2 text-center border rounded mb-4">
                    <h5 class="m-0"><strong>Create Password</strong></h5>
                </div>
                <form action="{{ route('AutoCare.customer.update-password', ['id' => $customer->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>New Password</label>
                                <input class="form-control" type="password" name="new_password" required minlength="8">
                                @error('new_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input class="form-control" type="password" name="new_password_confirmation" required
                                    minlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>


            <!-- Billing and Shipping Section -->
            <div class="mb-5">
                <div class="bg-light p-2 text-center border rounded mb-4">
                    <h5 class="m-0"><strong>Billing and Shipping</strong></h5>
                </div>
                <!-- Billing Address Form -->
                <form action="{{ route('AutoCare.customer.update-billing-address', ['id' => $customer->id]) }}"
                    method="POST">
                    @csrf
                    <h6><strong>Billing Address</strong></h6>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Street</label>
                                <input class="form-control" type="text" name="billing_address_street"
                                    value="{{ old('billing_address_street', $customer->billing_address_street) }}" required
                                    minlength="3" maxlength="100">
                                @error('billing_address_street')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>City</label>
                                <input class="form-control" type="text" name="billing_address_city"
                                    value="{{ old('billing_address_city', $customer->billing_address_city) }}" required
                                    minlength="3" maxlength="50">
                                @error('billing_address_city')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Postcode</label>
                                <input class="form-control" type="text" name="billing_address_postcode"
                                    value="{{ old('billing_address_postcode', $customer->billing_address_postcode) }}"
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
                                        <option value="{{ $county->zone_id }}" {{ old('billing_address_county', $customer->billing_address_county) == $county->zone_id ? 'selected' : '' }}>
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
                                    <option value="1" {{ old('billing_address_country', $customer->billing_address_country) == '1' ? 'selected' : '' }}>United Kingdom
                                    </option>
                                </select>
                                @error('billing_address_country')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3"><button type="submit" class="btn btn-primary">Update Billing
                            Address</button></div>
                </form>

                <!-- Shipping Address Form -->
                <form action="{{ route('AutoCare.customer.update-shipping-address', ['id' => $customer->id]) }}"
                    method="POST" class="mt-4">
                    @csrf
                    <h6><strong>Shipping Address</strong></h6>
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
                                <input class="form-control" type="text" name="shipping_address_street"
                                    id="shipping_address_street"
                                    value="{{ old('shipping_address_street', $customer->shipping_address_street) }}"
                                    required minlength="3">
                                @error('shipping_address_street')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>City</label>
                                <input class="form-control" type="text" name="shipping_address_city"
                                    id="shipping_address_city"
                                    value="{{ old('shipping_address_city', $customer->shipping_address_city) }}" required
                                    minlength="3">
                                @error('shipping_address_city')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Postcode</label>
                                <input class="form-control" type="text" name="shipping_address_postcode"
                                    id="shipping_address_postcode"
                                    value="{{ old('shipping_address_postcode', $customer->shipping_address_postcode) }}"
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
                                        <option value="{{ $county->zone_id }}" {{ old('shipping_address_county', $customer->shipping_address_county) == $county->zone_id ? 'selected' : '' }}>
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
                                    <option value="1" {{ old('shipping_address_country', $customer->shipping_address_country) == '1' ? 'selected' : '' }}>United
                                        Kingdom</option>
                                </select>
                                @error('shipping_address_country')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3"><button type="submit" class="btn btn-primary">Update Shipping
                            Address</button></div>
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
                document.getElementById('shipping_address_street').value = "{{ old('shipping_address_street', $customer->shipping_address_street) }}";
                document.getElementById('shipping_address_city').value = "{{ old('shipping_address_city', $customer->shipping_address_city) }}";
                document.getElementById('shipping_address_postcode').value = "{{ old('shipping_address_postcode', $customer->shipping_address_postcode) }}";
                document.getElementById('shipping_address_county').value = "{{ old('shipping_address_county', $customer->shipping_address_county) }}";
                document.getElementById('shipping_address_country').value = "{{ old('shipping_address_country', $customer->shipping_address_country) }}";
            }
        });
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const customerTypeSelect = document.getElementById('customerType');
    const individualFields = document.getElementById('individualFields');
    const tradeFields = document.getElementById('tradeFields');

    // Function to toggle visibility
    function toggleFields() {
        const selected = customerTypeSelect.value;

        if (selected === 'individual') {
            individualFields.style.display = 'block';
            tradeFields.style.display = 'none';

            // Remove required from trade fields
            document.querySelectorAll('#tradeFields input').forEach(input => {
                input.required = false;
            });

            // Add required back to individual fields
            document.querySelector('input[name="customer_name"]').required = true;
            document.querySelector('input[name="customer_last_name"]').required = false; // Optional
            document.querySelector('input[name="company_name"]').required = false;
        } else if (selected === 'trade') {
            individualFields.style.display = 'none';
            tradeFields.style.display = 'block';

            // Remove required from individual fields
            document.querySelector('input[name="customer_name"]').required = false;
            document.querySelector('input[name="customer_last_name"]').required = false;

            // Add required to trade fields
            document.querySelector('input[name="company_name"]').required = true;
            document.querySelector('input[name="contact_person"]').required = true;
            document.querySelector('input[name="contact_email"]').required = true;
        }
    }

    // Initial check
    toggleFields();

    // Listen for change
    customerTypeSelect.addEventListener('change', toggleFields);
});
</script>
@endsection