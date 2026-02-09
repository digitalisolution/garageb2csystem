@extends('layouts.app')

@section('content')
                <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
                    <div class="container">
                        <div class="breadcrumb-content text-center">
                            <ul>
                                <li>
                                    <a href="#">Home</a>
                                </li>
                                <li class="active">Checkout</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="checkout-area pt-70 pb-70">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-7">
                                @if(in_array($userOrdertype, $calenderBook))
                                    @include('calendar', ['events' => $events])
                                    @endif
                                    <div id="errorContainer" class="alert alert-danger" style="display: none;"></div>
                                <div>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @if ($errors->has('email'))
                                                    <li>{{ $errors->first('email') }}</li>
                                                @else
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    @endif

                                    <input type="hidden" id="selected_slot_details" name="selected_slot_details">
                                </div>
                                <form action="{{ route('checkout.submit') }}" method="POST" id="orderForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="billing-info-wrap">
                                                <h3>Billing Details</h3>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="customer_name">First Name <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" id="customer_name" name="customer_name"
                                                                value="{{ old('customer_name', $billingDetails['customer_name'] ?? '') }}" required>
                                                            <span id="customer_name_error" class="text-danger"></span>
                                                            @error('customer_name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="last_name">Last Name <span class="text-red">*</span></label>
                                                            <input type="text" id="last_name" name="last_name"
                                                                value="{{ old('last_name', $billingDetails['last_name'] ?? '') }}" required>
                                                            <span id="last_name_error" class="text-danger"></span>
                                                            @error('last_name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="email">Email <span class="text-red">*</span></label>
                                                            <input type="email" id="email" name="email"
                                                                value="{{ old('email', $billingDetails['email'] ?? '') }}" required>
                                                            <span id="email_error" class="text-danger"></span>
                                                            @error('email')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="phone_number">Phone Number <span
                                                                    class="text-red">*</span></label>
                                                            <input type="text" id="phone_number"
                                                                value="{{ old('phone_number', $billingDetails['phone_number'] ?? '') }}" name="phone_number"
                                                                required>
                                                            <span id="phone_number_error" class="text-danger"></span>
                                                            @error('phone_number')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="reg_number">Registration Number <span
                                                                    class="text-red">*</span></label>

                                                            @if (isset($vehicleDetails) && $vehicleDetails->isNotEmpty())
                                                                <select id="reg_number" name="reg_number" required>
                                                                    <option value="" disabled>Select a vehicle</option>
                                                                    @foreach ($vehicleDetails as $id => $regNumber)
                                                                        <option value="{{ $regNumber }}" {{ $loop->first ? 'selected' : '' }}>
                                                                            {{ $regNumber }}
                                                                        </option>
                                                                    @endforeach
                                                                    <option value="new">Enter a new vehicle registration number</option>
                                                                </select>
                                                            @else
                                                                <input type="text" id="reg_number" name="reg_number"
                                                                    value="{{ strtoupper($billingDetails['reg_number'] ?? session('reg_number', '')) }}"
                                                                    required>

                                                            @endif


                                                            <span id="reg_number_error" class="text-danger"></span>
                                                            @error('reg_number')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="postcode">Post Code <span class="text-red">*</span></label>
                                                            <input type="text" name="postcode" id="postcode" readonly
                                                                value="{{ $billingDetails['postcode'] ?? session('postcode') ?? session('user_postcode') }}"
                                                                onkeyup="this.value = this.value.toUpperCase()" required>
                                                            <span id="postcode_error" class="text-danger"></span>
                                                            @error('postcode')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="company">Company</label>
                                                            <input type="text" id="company"
                                                                value="{{ old('company_name', $billingDetails['company_name'] ?? '') }}"
                                                                name="company_name">
                                                            <span id="company_error" class="text-danger"></span>
                                                            @error('company')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="additional-info-wrap">
                                                            <div class="additional-info mb-20">
                                                                <label for="address">Address <span class="text-red">*</span></label>
                                                                <textarea id="address" name="address"
                                                                    value="{{ $billingDetails['address'] ?? '' }}" rows="3"
                                                                    required>{{ $billingDetails['address'] ?? '' }}</textarea>
                                                                <span id="address_error" class="text-danger"></span>
                                                                @error('address')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="city">City <span class="text-red">*</span></label>
                                                            <input type="text" id="city" value="{{ old('city', $billingDetails['city'] ?? '') }}"
                                                                name="city" required>
                                                            <span id="city_error" class="text-danger"></span>
                                                            @error('city')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="county">County <span class="text-red">*</span></label>
                                                            <select id="county" name="county" class="form-control" required>
                                                                <option value="" disabled {{ !$selectedCounty ? 'selected' : '' }}>
                                                                    Select a county</option>
                                                                @foreach ($counties as $id => $name)
                                                                    <option value="{{ $id }}" {{ old('county', $selectedCounty) == $id ? 'selected' : '' }}>
                                                                        {{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <span id="County_error" class="text-danger"></span>
                                                            @error('county')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="billing-info mb-20">
                                                            <label for="country">Country <span class="text-red">*</span></label>
                                                            <select id="country" name="country" class="form-control" required>
                                                                <option value="" disabled {{ !$selectedCountry ? 'selected' : '' }}>
                                                                    Select a county</option>
                                                            @foreach ($countries as $id => $name)
                                                                <option value="{{ $id }}" {{ old('country', $selectedCountry) == $id ? 'selected' : '' }}>
                                                                    {{ $name }}
                                                                </option>
                                                            @endforeach
                                                            </select>
                                                            <span id="Country_error" class="text-danger"></span>
                                                            @error('country')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="additional-info-wrap">
                                                            <div class="additional-info mb-20">
                                                                <label for="comment">Comment/Notes</label>
                                                                <textarea id="comment" name="comment" rows="3">{{ old('comment') }}</textarea>
                                                                <span id="comment_error" class="text-danger"></span>
                                                                @error('comment')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="your-order-area privacy-nav">
                                    <h3>Your order</h3>
                                    <div class="px-5 py-4 mb-3 bg-gray">
                                        <div class="garage_description">
                                        @php
    $logoPath = "frontend/{$domain}/img/garage_logo/{$garages->garage_logo}";
    $themeLogo = "frontend/themes/{$garages->theme}/img/garage_logo/{$garages->garage_logo}";
    $defaultLogo = "frontend/themes/default/img/logo/logo.png";
    $src = file_exists(public_path($logoPath)) ? $logoPath :
        (file_exists(public_path($themeLogo)) ? $themeLogo : $defaultLogo);
                                        @endphp
                                        <a href="{{ route('garage.profile', $garages->id) }}">
                                            <img src="{{ asset($src) }}?v={{ time() }}" alt="Logo" loading="lazy">
                                        </a>
                                        <div class="mt-3">
                                        <h4>{{ $garages->garage_name }}</h4>
                                        <span>{{ $garages->garage_street . ', ' . $garages->garage_city . ', ' . $garages->garage_zone . ', ' . $garages->garage_country }}</span>
                                    </div>
                                        </div>
                                    </div>
                                    <div class="your-order-wrap gray-bg-4">

                                        @if(isset($message) && $message)
                                            <div class="alert alert-warning">
                                                {{ $message }}
                                            </div>
                                            <script>
                                                window.location.href = '{{ route("home") }}';
                                            </script>
                                        @else
                                            <div id="cart-container">
                                                @include('cart', ['cartItems' => $cartItems, 'total' => $total])
                                            </div>
                                        @endif
                                        <div class="payment-method">
                                            <div class="element-mrg">
                                                <div class="panel-group" id="accordion">
                                                    @if(get_option('paymentmethod_payatfitting_active') == '1')
                                                        <div class="panel payment-accordion">
                                                            <div class="panel-heading" id="method-one">
                                                                <h4 class="panel-title">
                                                                    <a data-bs-toggle="collapse" href="#method1">
                                                                        Pay at Fitting Center <i class="fa fa-credit-card pull-right"></i>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="method1" class="panel-collapse collapse show"
                                                                data-bs-parent="#accordion">
                                                                <div class="panel-body">
                                                                    <div class="payment-mode">
                                                                        <label>
                                                                            <input type="radio" name="payment_method"
                                                                                value="pay_at_fitting_center" required checked> Pay at
                                                                            Fitting
                                                                            Center
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(get_option('paymentmethod_globalpay_active') == '1')
                                                        <div class="panel payment-accordion">
                                                            <div class="panel-heading" id="method-two">
                                                                <h4 class="panel-title">
                                                                    <a data-bs-toggle="collapse" href="#method2">
                                                                        Pay at Card <i class="fa fa-credit-card pull-right"></i>
                                                                    </a>
                                                                </h4>
                                                            </div>

                                                            <div id="method2" class="panel-collapse collapse show"
                                                                data-bs-parent="#accordion">
                                                                <div class="panel-body">
                                                                    <div class="payment-mode">
                                                                        <label>
                                                                            <input type="radio" name="payment_method" value="global_payment"
                                                                                required> Credit/Debit Card
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(get_option('paymentmethod_dojo_active') == '1')
                                                        <div class="panel payment-accordion">
                                                            <div class="panel-heading" id="method-three">
                                                                <h4 class="panel-title">
                                                                    <a data-bs-toggle="collapse" href="#method3">
                                                                        Pay at Card <i class="fa fa-credit-card pull-right"></i>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="method3" class="panel-collapse collapse show"
                                                                data-bs-parent="#accordion">
                                                                <div class="panel-body">
                                                                    <div class="payment-mode">
                                                                        <label>
                                                                            <input type="radio" name="payment_method" value="dojo" required>
                                                                            Credit/Debit Card
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(get_option('paymentmethod_paymentassist_active') == '1')
                                                        <div class="panel payment-accordion">
                                                            <div class="panel-heading" id="method-three">
                                                                <h4 class="panel-title">
                                                                    <a data-bs-toggle="collapse" href="#method3">
                                                                        Pay at Card({{ get_option('paymentmethod_paymentassist_label')  }})
                                                                        <i class="fa fa-credit-card pull-right"></i>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="method3" class="panel-collapse collapse show"
                                                                data-bs-parent="#accordion">
                                                                <div class="panel-body">
                                                                    <div class="payment-mode">
                                                                        <label>
                                                                            <input type="radio" name="payment_method" value="paymentassist" required>
                                                                            Credit/Debit Card
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(get_option('paymentmethod_revolut_active') == '1')
                                                        <div class="panel payment-accordion">
                                                            <div class="panel-heading" id="method-three">
                                                                <h4 class="panel-title">
                                                                    <a data-bs-toggle="collapse" href="#method3">
                                                                        Pay at Card({{ get_option('paymentmethod_revolut_label')  }})
                                                                        <i class="fa fa-credit-card pull-right"></i>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="method3" class="panel-collapse collapse show"
                                                                data-bs-parent="#accordion">
                                                                <div class="panel-body">
                                                                    <div class="payment-mode">
                                                                        <label>
                                                                            <input type="radio" name="payment_method" value="revolut" required>
                                                                            Credit/Debit Card
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="your-order-area">
                                            <div class="Place-order mt-25">
                                                <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
                                                <button type="submit" id="submitButton" class="btn-hover">Place Order</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            </form>
                        </div>
                        @if(session('user_ordertype') === 'fully_fitted')
                            <div class="col-md-12">
                                <x-garage-services :garage="$garage" :services="$services" />
                            </div>
                        @endif
                    </div>
                </div>
@endsection

@push('scripts')

    <script>
        document.getElementById('orderForm').addEventListener('submit', function () {
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
        });
    </script>
    <script>
        const isLoggedIn = {{ Auth::guard('customer')->check() ? 'true' : 'false' }};
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const regNumberSelect = document.getElementById('reg_number');
            const regNumberInputContainer = document.createElement('div');
            regNumberInputContainer.innerHTML = `<input type="text" id="new_reg_number"  name="new_reg_number"  placeholder="Enter new registration number"  style="display: none;"  > `;
            regNumberSelect.parentNode.appendChild(regNumberInputContainer);
            initializeRegNumberLogic();
        });
        function initializeRegNumberLogic() {
            const regNumberSelect = document.getElementById('reg_number');
            const newRegNumberInput = document.getElementById('new_reg_number');

            if (!regNumberSelect || !newRegNumberInput) {
                console.error('One or more required elements are missing from the DOM.');
                return;
            }

            regNumberSelect.addEventListener('change', function () {
                if (regNumberSelect.value === 'new') {
                    newRegNumberInput.style.display = 'block';
                    newRegNumberInput.required = true;
                } else {
                    newRegNumberInput.style.display = 'none';
                    newRegNumberInput.required = false;
                }
            });
        }

        $(document).ready(function () {
            const cartItems = {!! json_encode($cartItems) !!};
            const hasMobileFitting = cartItems.some(item => item.fitting_type === 'mobile_fitted');
            const postcodeData = {!! json_encode(session('postcode_data')) !!};
            const postcodeField = $('#postcode');
            const messageContainer = $('<div id="postcode-message" class="message"></div>');
            postcodeField.after(messageContainer);

            if (hasMobileFitting) {
                if (postcodeData && postcodeData.postcode) {
                    const sessionPostcode = postcodeData.postcode.trim();
                    const inputPostcode = postcodeField.val().trim();
                    if (inputPostcode !== sessionPostcode) {
                        postcodeField.val(sessionPostcode);
                    }
                } else {
                    messageContainer.text('Please enter a postcode to fetch shipping prices.').addClass('error');
                    postcodeField.focus();
                }
                postcodeField.on('blur', function () {
                    const postcode = $(this).val().trim();
                    messageContainer.removeClass('error success').text('');

                    if (postcode) {
                        $.ajax({
                            url: "{{ route('calculateShipping') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                postcode: postcode
                            },
                            success: function (response) {
                                if (response.success) {
                                    const calloutCharges = response.ship_price || 0;
                                    $('#shippingPrice').text(`£${calloutCharges.toFixed(2)}`);
                                    updateCart();
                                    messageContainer.text('Callout charges updated successfully.').addClass('success');

                                } else {
                                    messageContainer.text(response.message || 'Failed to fetch shipping prices.').addClass('error');
                                    postcodeField.val('');
                                    postcodeField.focus();
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('AJAX Error:', { status, error, xhr });
                                messageContainer.text('Sorry, we are not covering that area.').addClass('error');
                                postcodeField.val('');
                                postcodeField.focus();
                            }
                        });
                    } else {
                        messageContainer.text('Please enter a valid postcode.').addClass('error');
                    }
                });
                $('#checkout-form').on('submit', function (e) {
                    if (hasMobileFitting && !postcodeField.val().trim()) {
                        e.preventDefault();
                        messageContainer.text('Please enter a postcode to proceed.').addClass('error');
                        postcodeField.focus();
                    }
                });
            }
            function updateCart() {
                $.ajax({
                    url: "{{ route('cart.refresh') }}",
                    type: "GET",
                    success: function (response) {
                        $('#cart-container').html(response);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

    </script>

    <style>
        .message {
            margin-top: 5px;
            font-size: 14px;
            padding: 5px;
            border-radius: 4px;
            display: none;
        }

        .message.error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            display: block;
        }

        .message.success {
            color: #5cb85c;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            display: block;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.querySelector('form');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                try {

                      @if(in_array($userOrdertype, $calenderBook))
            // Only require calendar slot if order type is eligible
            const selectedSlot = collectSelectedCalendarSlot();
            const selectedSlotField = document.getElementById('selected_slot_details');
            if (!selectedSlotField) throw new Error("Hidden field 'selected_slot_details' not found.");
            selectedSlotField.value = JSON.stringify(selectedSlot);
            @endif
                    
                    form.submit();
                } catch (error) {
                    displayErrorMessage(error.message);
                }
            });
            function displayErrorMessage(message) {
                let errorMessageContainer = document.getElementById('error-message');
                if (!errorMessageContainer) {
                    errorMessageContainer = document.createElement('div');
                    errorMessageContainer.id = 'error-message';
                    errorMessageContainer.style.color = 'red';
                    errorMessageContainer.style.marginBottom = '10px';
                    form.prepend(errorMessageContainer);
                }

                errorMessageContainer.textContent = message;
            }

            const collectSelectedCalendarSlot = () => {
                const selectedSlotElement = document.getElementById('selectedSlot');
                if (!selectedSlotElement || !selectedSlotElement.textContent.trim()) {
                    throw new Error('Please select a booking slot from the calendar.');
                }

                return { slot: selectedSlotElement.textContent.trim() };
            };
            const collectCustomerDetails = () => {
                const customerDetails = {
                    customer_name: document.getElementById('customer_name')?.value.trim() || '',
                    last_name: document.getElementById('last_name')?.value.trim() || '',
                    email: document.getElementById('email')?.value.trim() || '',
                    phone_number: document.getElementById('phone_number')?.value.trim() || '',
                    address: document.getElementById('address')?.value.trim() || '',
                    postcode: document.getElementById('postcode')?.value.trim() || '',
                    city: document.getElementById('city')?.value.trim() || '',
                    country: document.getElementById('country')?.value.trim() || '',
                    company: document.getElementById('company')?.value.trim() || '',
                    comment: document.getElementById('comment')?.value.trim() || '',
                };

                const errors = {};
                if (!customerDetails.customer_name) {
                    errors.customer_name = 'First name is required.';
                } else if (!/^[a-zA-Z\s]+$/.test(customerDetails.customer_name)) {
                    errors.customer_name = 'First name should only contain letters and spaces.';
                }
                if (!customerDetails.last_name) {
                    errors.last_name = 'Last name is required.';
                } else if (!/^[a-zA-Z\s]+$/.test(customerDetails.last_name)) {
                    errors.last_name = 'Last name should only contain letters and spaces.';
                }
                if (!customerDetails.email) {
                    errors.email = 'Email is required.';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(customerDetails.email)) {
                    errors.email = 'Please enter a valid email address.';
                }
                if (!customerDetails.phone_number) {
                    errors.phone_number = 'Phone number is required.';
                } else if (!/^\+?[0-9]{10,15}$/.test(customerDetails.phone_number)) {
                    errors.phone_number = 'Phone number should be 10-15 digits';
                }
                if (!customerDetails.address) {
                    errors.address = 'Address is required.';
                } else if (customerDetails.address.length > 500) {
                    errors.address = 'Address should not exceed 500 characters.';
                }
                if (!customerDetails.postcode) {
                    errors.postcode = 'Postcode is required.';
                } else if (!/^[A-Za-z0-9\s]{3,10}$/.test(customerDetails.postcode.trim())) {
                    errors.postcode = 'Please enter a valid postcode (3 to 10 characters, letters/numbers, spaces allowed).';
                }
                if (!customerDetails.city) {
                    errors.city = 'City is required.';
                } else if (!/^[a-zA-Z\s]+$/.test(customerDetails.city)) {
                    errors.city = 'City name should only contain letters and spaces.';
                }
                if (!customerDetails.country) {
                    errors.country = 'Country is required.';
                } else if (!/^\+?[0-9]{1,15}$/.test(customerDetails.country)) {
                    errors.country = 'Country name should only contain letters and spaces.';
                }
                if (customerDetails.company && customerDetails.company.length > 100) {
                    errors.company = 'Company name should not exceed 100 characters.';
                }
                if (customerDetails.comment && customerDetails.comment.length > 500) {
                    errors.comment = 'Comment/Notes should not exceed 500 characters.';
                }
                if (Object.keys(errors).length > 0) {
                    throw new Error(JSON.stringify(errors));
                }

                return customerDetails;
            };
            function displayErrors(errors) {
                document.querySelectorAll('.text-danger').forEach(errorElement => {
                    errorElement.textContent = '';
                });
                Object.keys(errors).forEach(field => {
                    const errorElement = document.querySelector(`#${field}_error`);
                    if (errorElement) {
                        errorElement.textContent = errors[field];
                    }
                });
            }
            document.querySelector('form').addEventListener('submit', async (e) => {
                e.preventDefault();

                try {
                    const customerDetails = collectCustomerDetails();
                } catch (error) {
                    const errors = JSON.parse(error.message);
                    displayErrors(errors);
                }
            });
        });

    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isLoggedIn = {{ Auth::guard('customer')->check() ? 'true' : 'false' }};
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email_error');
            function saveFormDataToSession() {
                if (!isLoggedIn) {
                    const formFields = document.querySelectorAll('#orderForm input,#orderForm select, #orderForm textarea');
                    formFields.forEach(field => {
                        field.addEventListener('blur', function () {
                            const fieldName = this.name;
                            const fieldValue = this.value;
                            fetch('{{ route('checkout.storeInSession') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({ fieldName, fieldValue }),
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Trigger saving customer to database when session is updated
                                        // saveCustomerToDatabase();
                                    }
                                })
                                .catch(error => console.error('Error updating session:', error));
                        });
                    });
                }


            }

            // Save customer data to database
            const saveCustomerToDatabase = () => {
                fetch('{{ route('checkout.autoSaveCustomer') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({}),
                })
                    .then(response => response.text())
                    .then(data => {
                        try {
                            const jsonData = JSON.parse(data);
                            if (jsonData.success) {
                                // console.log('Customer saved successfully:');
                            } else {
                                console.error('Error saving customer:', jsonData.error);
                            }
                        } catch (e) {
                            console.error('Failed to parse JSON:', e);
                        }
                    })
                    .catch(error => console.error('Error saving customer to database:', error));
            };
        });
    </script>
@endpush