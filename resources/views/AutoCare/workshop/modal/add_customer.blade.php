<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Profile Section -->
                <div class="mb-2">
                    <div class="bg-dark p-1 text-center rounded mb-2">
                        <h6 class="m-0 text-white"><strong>Profile</strong></h6>
                    </div>
                    <form id="addCustomerForm" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="add_new_customer">
                        <div class="item">
                            <div class="form-group">
                                <label>Name/Company<span class="text-red">*</span></label>
                                <input class="form-control" type="text" name="customer_name" required minlength="2"
                                    maxlength="50">
                                @error('customer_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="item">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input class="form-control" type="text" name="customer_last_name" minlength="2"
                                    maxlength="50">
                                @error('customer_last_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> -->
                        <div class="item">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="form-control" type="email" name="customer_email">
                                @error('customer_email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="item">
                            <div class="form-group">
                                <label>Telephone</label>
                                <input class="form-control" type="text" name="customer_contact_number" minlength="10"
                                    maxlength="15">
                                @error('customer_contact_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="item">
                            <div class="form-group">
                                <label>Alternate Telephone</label>
                                <input class="form-control" type="text" name="customer_alt_number" minlength="10"
                                    maxlength="15">
                                @error('customer_alt_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> -->
                        <div class="item d-none">
                            <div class="form-group">
                                <label>Company Name</label>
                                <input class="form-control" type="text" name="company_name" maxlength="100">
                                @error('company_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="item d-none">
                            <div class="form-group">
                                <label>Website</label>
                                <input class="form-control" type="text" name="company_website" maxlength="100">
                                @error('company_website')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Billing and Shipping Section -->

                <div class="bg-dark p-1 text-center rounded mb-2">
                    <h6 class="m-0 text-white"><strong>Billing and Shipping</strong></h6>
                </div>
                <!-- Billing Address Form -->

                <h6><strong>Billing Address</strong></h6>
                <div class="add_new_customer">
                    <div class="item">
                        <div class="form-group">
                            <label>Street</label>
                            <input class="form-control" type="text" name="billing_address_street" minlength="3"
                                maxlength="100">
                            @error('billing_address_street')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>City</label>
                            <input class="form-control" type="text" name="billing_address_city" minlength="3"
                                maxlength="50">
                            @error('billing_address_city')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>Postcode</label>
                            <input class="form-control" type="text" name="billing_address_postcode" minlength="3"
                                maxlength="10">
                            @error('billing_address_postcode')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>County</label>
                            <select class="form-control" name="billing_address_county">
                                <option>Select</option>
                                @foreach ($counties as $county)
                                    <option value="{{ $county->zone_id }}">
                                        {{ $county->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('billing_address_county')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>Country</label>
                            <select class="form-control" name="billing_address_country">
                                <option value="1">United Kingdom</option>
                            </select>
                            @error('billing_address_country')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Shipping Address Form -->

                <h6 class="mt-2"><strong>Shipping Address</strong></h6>
                <div class="form-group">
                    <div class="sidebar-widget-list-left">
                        <label class="remember-flex">
                            <input type="checkbox" name="same_as_billing" id="same_as_billing"> Same as Billing Address
                        </label>
                    </div>
                </div>
                <div class="add_new_customer">
                    <div class="item">
                        <div class="form-group">
                            <label>Street</label>
                            <input class="form-control" type="text" name="shipping_address_street"
                                id="shipping_address_street" minlength="3">
                            @error('shipping_address_street')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>City</label>
                            <input class="form-control" type="text" name="shipping_address_city"
                                id="shipping_address_city" minlength="3">
                            @error('shipping_address_city')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>Postcode</label>
                            <input class="form-control" type="text" name="shipping_address_postcode"
                                id="shipping_address_postcodes" minlength="3">
                            @error('shipping_address_postcode')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>County</label>
                            <select class="form-control" name="shipping_address_county" id="shipping_address_county">
                                <option>Select</option>
                                @foreach ($counties as $county)
                                    <option value="{{ $county->zone_id }}">
                                        {{ $county->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipping_address_county')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="item">
                        <div class="form-group">
                            <label>Country</label>
                            <select class="form-control" name="shipping_address_country" id="shipping_address_country">
                                <option value="1">United
                                    Kingdom</option>
                            </select>
                            @error('shipping_address_country')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3 pb-0 pr-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCustomerButton">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('same_as_billing').addEventListener('change', function () {
        if (this.checked) {
            // Copy billing address fields to shipping address fields
            document.getElementById('shipping_address_street').value = document.querySelector('[name="billing_address_street"]').value;
            document.getElementById('shipping_address_city').value = document.querySelector('[name="billing_address_city"]').value;
            document.getElementById('shipping_address_postcodes').value = document.querySelector('[name="billing_address_postcode"]').value;
            document.getElementById('shipping_address_county').value = document.querySelector('[name="billing_address_county"]').value;
            document.getElementById('shipping_address_country').value = document.querySelector('[name="billing_address_country"]').value;
        }
    });

</script>
<script>
$(function () {
    $('#saveCustomerButton').on('click', function () {
         const form = document.getElementById('addCustomerForm');
    const formData = new FormData(form);

    formData.append('_token', '{{ csrf_token() }}');
    for (let [key, value] of formData.entries()) {
        // console.log(key, value);
    }
        // Basic validation
        if (!formData) {
            alert('First Name is required');
            return;
        }

        $.ajax({
            url: '{{ url("AutoCare/workshop/createCustomer") }}',
            type: 'POST',
            data: formData,
             contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    alert('Customer added successfully!');
                    $('#addCustomerModal').modal('hide');

                    // Add new customer to select dropdown if needed
                    const newOption = new Option(response.customer.customer_name, response.customer.id, true, true);
                    $('#customer_id').append(newOption).trigger('change');

                    // Optionally reset fields manually
                    $('#addCustomerForm')[0].reset();
                } else {
                    alert(response.message || 'Failed to add customer.');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Error occurred while saving customer.');
            }
        });
    });
});
</script>
