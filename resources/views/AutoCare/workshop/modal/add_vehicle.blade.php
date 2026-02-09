<!-- Modal for Adding a New Vehicle -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Add New Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVehicleForm" method="POST">
                    @csrf
                    <div class="edit_vehicle_plate mb-4">
                        <div class="plate_wrap">
                            <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" height="48">
                            <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="add_vehicle_reg_number" name="vrm">
                        </div>
                        <button type="button" id="vrmlookupButton" class="btn btn-primary px-4">Get Vehicle
                            Details</button>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Vehicle Category</label>
                                <input type="text" name="vehicle_category" id="add_vehicle_category" class="form-control"
                                    value="">
                                @error('vehicle_category')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>VRM (Vehicle Registration Mark)</label>
                                <input type="text" name="vehicle_reg_number" id="add_vrm" class="form-control"
                                    value="">
                                @error('vehicle_reg_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" name="vehicle_make" id="add_make" class="form-control"
                                    value="">
                                @error('make')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Model</label>
                                <input type="text" name="vehicle_model" id="add_model" class="form-control"
                                    value="">
                                @error('model')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" name="vehicle_year" id="add_year" class="form-control"
                                    value="">
                                @error('year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>CC</label>
                                <input type="text" name="vehicle_cc" id="add_cc" class="form-control"
                                    value="">
                                @error('cc')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Fuel Type</label>
                                <input type="text" name="vehicle_fuel_type" id="add_fuel_type" class="form-control"
                                    value="">
                                @error('fuel_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Body Type</label>
                                <input type="text" name="vehicle_body_type" id="add_body_type" class="form-control"
                                    value="">
                                @error('body_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>BHP</label>
                                <input type="text" name="vehicle_bhp" id="add_bhp" class="form-control"
                                    value="">
                                @error('bhp')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Number</label>
                                <input type="text" name="vehicle_engine_number" id="add_engine_number" class="form-control"
                                    value="">
                                @error('engine_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Size</label>
                                <input type="text" name="vehicle_engine_size" id="add_engine_size" class="form-control"
                                    value="">
                                @error('engine_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Code</label>
                                <input type="text" name="vehicle_engine_code" id="add_engine_code" class="form-control"
                                    value="">
                                @error('engine_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>VIN</label>
                                <input type="text" name="vehicle_vin" id="add_vin" class="form-control"
                                    value="">
                                @error('vin')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Front Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_front_tyre_size" id="add_front_tyre_size" class="form-control"
                                    value="">
                                @error('front_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Rear Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_rear_tyre_size" id="add_rear_tyre_size" class="form-control"
                                    value="">
                                @error('rear_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Colour</label>
                                <input type="text" name="vehicle_colour" id="add_colour" class="form-control"
                                    value="">
                                @error('colour')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>First Registered</label>
                                <input type="date" name="vehicle_first_registered" id="add_first_registered" class="form-control"
                                    value="">

                                @error('first_registered')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Chassis No.</label>
                                <input type="text" name="vehicle_chassis_no" id="add_chassis_no" class="form-control"
                                    value="">
                                @error('chassis_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Torque Settings</label>
                                <input type="text" name="vehicle_torque_settings" id="add_torque_settings" class="form-control"
                                    value="">
                                @error('torque_settings')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>MOT Expiry Date</label>
                                <input type="date" name="vehicle_mot_expiry_date" id="add_mot_expiry_date" class="form-control"
                                    value="">
                                @error('mot_expiry_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-center mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveVehicleButton">Save</button>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function () {
    // Handle customer selection
    $('[name=customer_id]').on("change", function () {
        var customerId = $(this).val();

        if (customerId) {
            $.ajax({
                type: "POST",
                url: "{{ url('/') }}/ajax/GetVehicleRegFromWorkshop",
                data: {
                    "_token": "{{ csrf_token() }}",
                    customer_id: customerId,
                },
                dataType: 'html',
                cache: false,
                success: function (data) {
                    const vehicleRegNum = JSON.parse(data);

                    // Populate the vehicle dropdown
                    $('#registered_vehicle').empty()
                        .append('<option value="">- Select Vehicle -</option>');
                    vehicleRegNum.forEach(vehicle => {
                        $('#registered_vehicle').append(`<option value="${vehicle.vehicle_reg_number}">${vehicle.vehicle_reg_number}</option>`);
                    });

                    // Show the vehicle dropdown
                    $('#registered_vehicleHS').show();
                }
            });
        } else {
            $('#registered_vehicleHS').hide();
        }
    });

    // Handle vehicle selection
    $('#registered_vehicle').on("change", function () {
        const selectedVehicle = $(this).val();
        if (selectedVehicle) {
            // Hide the Lookup button and show the Add Vehicle button
            $('#lookupButton').addClass('d-none');
            $('#addVehicleButton').removeClass('d-none');
            document.activeElement.blur();
            // Populate the vehicle_reg_number field
            $('#vehicle_reg_number').val(selectedVehicle);
        } else {
            // Reset to default state
            document.activeElement.blur();
            $('#lookupButton').removeClass('d-none');
            $('#addVehicleButton').addClass('d-none');
            $('#vehicle_reg_number').val('');
        }
    });

    // Handle saving a new vehicle via modal
    $('#saveVehicleButton').on('click', function () {

        const formElement = document.getElementById('addVehicleForm');
        const formData = new FormData(formElement);

        // Convert FormData to plain object (if needed for JSON AJAX)
        const newVehicleData = {
        _token: formData.get('_token'),
        customer_id: $('[name=customer_id]').val(), // not in form, manually added
        };

        formData.forEach((value, key) => {
        newVehicleData[key] = value;
        });

        const customerId = $('[name=customer_id]').val();
       

        $.ajax({
            type: "POST",
            url: `/AutoCare/customer/${customerId}/vehicles`, 
            data: newVehicleData,
            success: function (response) {
                alert('Vehicle added successfully!');
                document.activeElement.blur();
                $('#addVehicleModal').modal('hide');

                // Refresh the vehicle dropdown
                const customerId = $('[name=customer_id]').val();
                $.ajax({
                    type: "POST",
                    url: "{{ url('/') }}/ajax/GetVehicleRegFromWorkshop",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        customer_id: customerId,
                    },
                    dataType: 'html',
                    cache: false,
                    success: function (data) {
                        const vehicleRegNum = JSON.parse(data);
                        const registeredVehicleDropdown = $('#registered_vehicle');
                        // Populate the vehicle dropdown
                        $('#registered_vehicle').empty()
                            .append('<option value="">- Select Vehicle -</option>');
                        vehicleRegNum.forEach(vehicle => {
                            $('#registered_vehicle').append(`<option value="${vehicle.vehicle_reg_number}">${vehicle.vehicle_reg_number}</option>`);
                        });
                        const newVehicleRegNumber = newVehicleData.vehicle_reg_number;
                        registeredVehicleDropdown.val(newVehicleRegNumber).trigger('change');
                    }
                });
            },
            error: function (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the vehicle.');
            }
        });
    });
});
</script>
<script>
       document.getElementById('vrmlookupButton').addEventListener('click', async function () {
    const lookupButton = document.getElementById('vrmlookupButton');
    const vrm = document.getElementById('add_vehicle_reg_number').value.trim();
    lookupButton.disabled = true;
    lookupButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Fetching...';

    if (!vrm) {
        alert('Please enter a valid VRM.');
        lookupButton.disabled = false;
        lookupButton.innerHTML = 'Lookup';
        return;
    }

    try {
        const response = await fetch(`${window.location.origin}/vehicle-mot-data?vrm=${vrm}`, {
            headers: {
                'X-Request-Token': '{{ env("API_REQUEST_TOKEN") }}',
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 429) {
            alert('Too many requests! Please wait a moment before trying again.');
            return;
        }

        const result = await response.json();
        // console.log('API Response:', result); // Debugging: Log the API response

        lookupButton.innerHTML = 'Lookup';

        if (response.ok && result.success && result.data) {
            const vehicleDetails = result.data.VehicleDetails?.VehicleIdentification || {};
            const color = result.data.VehicleDetails?.VehicleHistory || {};
            const SmmtDetails = result.data.SmmtDetails?.TechnicalDetails || {};
            const Performance = result.data.SmmtDetails?.Performance || {};
            const tyreDetails = result.data.TyreDetails?.TyreDetailsList?.[0] || {};
            const motHistory = result.data.MotHistoryDetails || {};
            const RapidVehicleDetails = result.data.RapidVehicleDetails?.VehicleClass || {};
            // console.log(vehicleDetails);
            // Populate vehicle details fields
            document.getElementById('add_vehicle_category').value = RapidVehicleDetails || '';
            document.getElementById('add_vrm').value = vehicleDetails.Vrm || '';
            document.getElementById('add_make').value = vehicleDetails.DvlaMake || '';
            document.getElementById('add_model').value = vehicleDetails.DvlaModel || '';
            document.getElementById('add_year').value = vehicleDetails.YearOfManufacture || '';
            document.getElementById('add_cc').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('add_fuel_type').value = SmmtDetails.FuelType || '';
            document.getElementById('add_body_type').value = SmmtDetails.BodyStyle || '';
            document.getElementById('add_bhp').value = Performance.PowerBhp || '';
            document.getElementById('add_engine_number').value = vehicleDetails.EngineNumber || '';
            document.getElementById('add_engine_size').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('add_engine_code').value = SmmtDetails.EngineDescription || '';
            document.getElementById('add_vin').value = vehicleDetails.VinLast5 || '';
            document.getElementById('add_front_tyre_size').value = tyreDetails.Front?.Tyre?.SizeDescription || '';
            document.getElementById('add_rear_tyre_size').value = tyreDetails.Rear?.Tyre?.SizeDescription || '';
            document.getElementById('add_colour').value = color?.ColourDetails?.CurrentColour || '';
            document.getElementById('add_first_registered').value = vehicleDetails.DateFirstRegisteredInUk.split("T")[0] || '';
            document.getElementById('add_chassis_no').value = vehicleDetails.VinLast5 || '';
            document.getElementById('add_torque_settings').value = Performance.TorqueNm || '';
            document.getElementById('add_mot_expiry_date').value = motHistory.MotDueDate.split("T")[0] || '';
        } else {
            alert(result.error || 'Unable to fetch VRM details.');
        }
    } catch (error) {
        console.error('Fetch Error:', error.message);
        alert('An error occurred while fetching VRM details.');
        lookupButton.innerHTML = 'Lookup';
    } finally {
        lookupButton.disabled = false;
    }
});

    </script>