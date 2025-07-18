<div class="modal fade" id="quoteEnquiryModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="quoteEnquiryForm" method="POST" action="{{ route('service.enquiry.submit') }}">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title" id="quoteModalLabel">Get a Quote</h3>
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
                                        <input type="text" name="vehicle_reg" value="{{$vehicleData['regNumber']}}"
                                            readonly class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Vehicle Mileage</label>
                                        <input type="text" name="mileage" class="form-control">
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
                                        <label>Company</label>
                                        <input type="text" name="company" class="form-control">
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
                                            @foreach ($counties as $id => $name)
                                                <option value="{{ $id }}">
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
                            <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="county">Country*</label>
                                        <select id="county" name="county" class="form-control" required>
                                            @foreach ($countries as $id => $name)
                                                <option value="{{ $id }}">
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
                            <h4>Frequently Selected Services*</h4>
                            <div class="quote_service-wrap">
                                @foreach($services as $service)
                                    <label for="service_{{ $service->service_id }}">
                                        {{ $service->name }}
                                        <input type="checkbox" name="selected_services[]" value="{{ $service->service_id }}"
                                            id="service_{{ $service->service_id }}"></label>
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
            <button type="submit" class="btn btn-theme border"><strong>Quote Now</strong></button>
        </div>
        </form>
    </div>
</div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-enquiry-modal').forEach(button => {
            button.addEventListener('click', function () {
                const serviceName = this.getAttribute('data-service');

                // Pre-select the service in the modal if needed
                document.querySelectorAll('#quoteEnquiryModal input[name="selected_services[]"]').forEach(input => {
                    input.checked = input.value === serviceName;
                });

                $('#quoteEnquiryModal').modal('show');
            });
        });
    });
</script>