<div class="modal fade right" id="quoteEnquiryModal" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="quoteEnquiryForm" method="POST" action="{{ route('service.enquiry.submit') }}">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title" id="quoteModalLabel">Request an Estimate</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column (Vehicle Info) -->
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Vehicle Reg.No*</label>
                                        @if(isset($vehicleData['regNumber']) && !empty($vehicleData['regNumber']))
                                            <input type="text" name="vehicle_reg" value="{{$vehicleData['regNumber']}}"
                                                readonly class="form-control" required>
                                        @else
                                            <input type="text" name="vehicle_reg" class="form-control" required>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>First Name*</label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>E-mail Address*</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Address*</label>
                                        <input type="text" name="address" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Postcode*</label>
                                        <input type="text" name="postcode" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>City*</label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="county">County*</label>
                                        <select id="county" name="county" class="form-control" required>
                                            <option value="">Select County</option>
                                            @foreach ($counties as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="country">Country*</label>
                                        <select id="country" name="country" class="form-control" required>
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $id => $name)
                                                <option value="{{ $id }}" selected>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="form-group">
                                        <label>Message</label>
                                        <textarea name="message" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column (Service Checkboxes) -->
                        <div class="col-md-5">
                            <div class="quote_selected_services">
                                <h4>Selected Services*</h4>
                                <div class="quote_service-wrap">
                                    @foreach($services as $service)
                                        <label for="service_{{ $service->service_id }}" class="quote_service-wrap-error">
                                            {{ $service->name }}
                                            <input type="checkbox" name="selected_services[]"
                                                value="{{ $service->service_id }}"
                                                id="service_{{ $service->service_id }}">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <x-recaptcha />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-theme border"><strong>Send Now</strong></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
        window.carImageConfig = {
        cdnBase: "{{ config('cdn.carbrands_cdn_url') }}",
        localPath: "{{ asset('frontend/themes/default/img/brand-logo/uniroyal.jpg') }}",
        defaultImage: "{{ asset('frontend/themes/default/img/brand-logo/uniroyal.jpg') }}"
    };
document.addEventListener('DOMContentLoaded', function () {
    // Handle modal trigger buttons
    document.querySelectorAll('.btn-enquiry-modal').forEach(button => {
        button.addEventListener('click', function () {
            const serviceId = this.getAttribute('data-id');

            // Pre-select only the matching service checkbox
            document.querySelectorAll('#quoteEnquiryModal input[name="selected_services[]"]').forEach(input => {
                input.checked = (input.value === serviceId);
            });

            $('#quoteEnquiryModal').modal('show');
        });
    });

    // Handle form submission
    const form = document.getElementById('quoteEnquiryForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submit
            
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<strong>Processing...</strong>';

            // Clear all previous error messages
            clearAllErrors();

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (response.status === 422) {
                    // Handle validation errors
                    return response.json().then(data => {
                        showValidationErrors(data.errors);
                        throw new Error('Validation failed');
                    });
                } else if (response.status === 200) {
                    // Handle success
                    return response.json();
                } else {
                    // Handle other errors
                    throw new Error('Server error: ' + response.status);
                }
            })
            .then(data => {
                if (data && data.success) {
                    // Success - close modal and show success message
                    $('#quoteEnquiryModal').modal('hide');
                    showSuccessMessage('Thank you! Your enquiry has been submitted successfully.');
                    form.reset();
                    clearAllErrors();
                } else {
                    throw new Error('Unexpected response format');
                }
            })
            .catch(error => {
                console.error("Error submitting form:", error);
                if (error.message !== 'Validation failed') {
                    showErrorMessage('An error occurred while submitting your enquiry. Please try again.');
                }
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }

    function clearAllErrors() {
        // Remove all existing error messages
        form.querySelectorAll('.text-danger').forEach(el => {
            if (!el.hasAttribute('data-server-error')) {
                el.remove();
            }
        });
        
        // Remove error classes from form controls
        form.querySelectorAll('.form-control').forEach(el => {
            el.classList.remove('is-invalid');
        });
    }

    function showValidationErrors(errors) {
        for (let fieldName in errors) {
            const errorMessages = errors[fieldName];
            let errorField = null;
            // Handle different field types
            if (fieldName === 'selected_services') {
                // For checkboxes, show error after the service wrap
                const serviceWrap = form.querySelector('.quote_service-wrap-error');
                if (serviceWrap && serviceWrap.parentElement) {
                    showErrorForElement(serviceWrap.parentElement, errorMessages[0]);
                }
            } else {
                // For regular input fields and selects
                errorField = form.querySelector(`[name="${fieldName}"]`);
                if (errorField) {
                    errorField.classList.add('is-invalid');
                    showErrorForElement(errorField.parentElement, errorMessages[0]);
                }
            }
        }
    }

    function showErrorForElement(container, message) {
        // Remove existing error message for this container
        const existingError = container.querySelector('.text-danger:not([data-server-error])');
        if (existingError) {
            existingError.remove();
        }

        // Create new error element
        const errorElement = document.createElement('span');
        errorElement.classList.add('text-danger');
        errorElement.style.display = 'block';
        errorElement.style.fontSize = '0.875rem';
        errorElement.style.marginTop = '0.25rem';
        errorElement.textContent = message;
        
        // Append error message
        container.appendChild(errorElement);
    }

    function showSuccessMessage(message) {
        // You can customize this based on your notification system
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function showErrorMessage(message) {
        // You can customize this based on your notification system
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        } else {
            alert(message);
        }
    }
});
</script>