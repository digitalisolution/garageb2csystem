@extends('layouts.app')

@section('content')
    <div class="pt-60 pb-60">
        <div class="container">
            @include('customer.menu')
            <div class="short__item mb-4">
                <div class="bg-gray p-3 text-center rounded mb-4 p-3 border rounded bg-light">
                    <h2 class="m-0"><strong>{{ isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle' }}</strong></h2>
                </div>
                <form
                    action="{{ isset($vehicle) ? route('customer.vehicles.update', $vehicle->id) : route('customer.vehicles.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($vehicle))
                        @method('PUT')
                    @endif
                    <div class="edit_vehicle_plate mb-4">
                        <div class="plate_wrap">
                            <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" height="48">
                            <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="vehicle_reg_number" name="vrm">
                        </div>
                        <button type="button" id="lookupButton" class="btn btn-theme-select px-4">Get Vehicle
                            Details</button>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Vehicle Category</label>
                                <input type="text" name="vehicle_category" id="vehicle_category" class="form-control"
                                    value="{{ old('vehicle_category', $vehicle->vehicle_category ?? '') }}">
                                @error('vehicle_category')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>VRM (Vehicle Registration Mark)</label>
                                <input type="text" name="vehicle_reg_number" id="vrm" class="form-control"
                                    value="{{ old('vehicle_reg_number', $vehicle->vehicle_reg_number ?? '') }}">
                                @error('vehicle_reg_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" name="vehicle_make" id="vehicle_make" class="form-control"
                                    value="{{ old('vehicle_make', $vehicle->vehicle_make ?? '') }}">
                                @error('vehicle_make')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Model</label>
                                <input type="text" name="vehicle_model" id="vehicle_model" class="form-control"
                                    value="{{ old('vehicle_model', $vehicle->vehicle_model ?? '') }}">
                                @error('vehicle_model')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" name="vehicle_year" id="vehicle_year" class="form-control"
                                    value="{{ old('vehicle_year', $vehicle->vehicle_year ?? '') }}">
                                @error('vehicle_year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>CC</label>
                                <input type="text" name="vehicle_cc" id="vehicle_cc" class="form-control"
                                    value="{{ old('vehicle_cc', $vehicle->vehicle_cc ?? '') }}">
                                @error('vehicle_cc')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Fuel Type</label>
                                <input type="text" name="vehicle_fuel_type" id="vehicle_fuel_type" class="form-control"
                                    value="{{ old('vehicle_fuel_type', $vehicle->vehicle_fuel_type ?? '') }}">
                                @error('vehicle_fuel_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Body Type</label>
                                <input type="text" name="vehicle_body_type" id="vehicle_body_type" class="form-control"
                                    value="{{ old('vehicle_body_type', $vehicle->vehicle_body_type ?? '') }}">
                                @error('vehicle_body_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>BHP</label>
                                <input type="text" name="vehicle_bhp" id="vehicle_bhp" class="form-control"
                                    value="{{ old('vehicle_bhp', $vehicle->vehicle_bhp ?? '') }}">
                                @error('vehicle_bhp')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Number</label>
                                <input type="text" name="vehicle_engine_number" id="vehicle_engine_number" class="form-control"
                                    value="{{ old('vehicle_engine_number', $vehicle->vehicle_engine_number ?? '') }}">
                                @error('vehicle_engine_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Size</label>
                                <input type="text" name="vehicle_engine_size" id="vehicle_engine_size" class="form-control"
                                    value="{{ old('vehicle_engine_size', $vehicle->vehicle_engine_size ?? '') }}">
                                @error('vehicle_engine_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Code</label>
                                <input type="text" name="vehicle_engine_code" id="vehicle_engine_code" class="form-control"
                                    value="{{ old('vehicle_engine_code', $vehicle->vehicle_engine_code ?? '') }}">
                                @error('vehicle_engine_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>VIN</label>
                                <input type="text" name="vehicle_vin" id="vehicle_vin" class="form-control"
                                    value="{{ old('vehicle_vin', $vehicle->vehicle_vin ?? '') }}">
                                @error('vehicle_vin')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Front Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_front_tyre_size" id="vehicle_front_tyre_size" class="form-control"
                                    value="{{ old('vehicle_front_tyre_size', $vehicle->vehicle_front_tyre_size ?? '') }}">
                                @error('vehicle_front_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Rear Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_rear_tyre_size" id="vehicle_rear_tyre_size" class="form-control"
                                    value="{{ old('vehicle_rear_tyre_size', $vehicle->vehicle_rear_tyre_size ?? '') }}">
                                @error('vehicle_rear_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Colour</label>
                                <input type="text" name="vehicle_colour" id="vehicle_colour" class="form-control"
                                    value="{{ old('vehicle_colour', $vehicle->vehicle_colour ?? '') }}">
                                @error('vehicle_colour')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>First Registered</label>
                                <input type="date" name="vehicle_first_registered" id="vehicle_first_registered" class="form-control"
                                    value="{{ old('vehicle_first_registered', isset($vehicle->vehicle_first_registered) ? \Carbon\Carbon::parse($vehicle->vehicle_first_registered)->format('Y-m-d') : '') }}">

                                @error('vehicle_first_registered')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Chassis No.</label>
                                <input type="text" name="vehicle_chassis_no" id="vehicle_chassis_no" class="form-control"
                                    value="{{ old('vehicle_chassis_no', $vehicle->vehicle_chassis_no ?? '') }}">
                                @error('vehicle_chassis_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Torque Settings</label>
                                <input type="text" name="vehicle_torque_settings" id="vehicle_torque_settings" class="form-control"
                                    value="{{ old('vehicle_torque_settings', $vehicle->vehicle_torque_settings ?? '') }}">
                                @error('vehicle_torque_settings')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>MOT Expiry Date</label>
                                <input type="date" name="vehicle_mot_expiry_date" id="vehicle_mot_expiry_date" class="form-control"
                                    value="{{ old('vehicle_mot_expiry_date', $vehicle->vehicle_mot_expiry_date ?? '') }}">
                                @error('vehicle_mot_expiry_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit"
                            class="btn btn-theme-select">{{ isset($vehicle) ? 'Update Vehicle' : 'Add Vehicle' }}</button>
                        <a href="{{ route('customer.vehicles') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
   <script>
       document.getElementById('lookupButton').addEventListener('click', async function () {
    const lookupButton = document.getElementById('lookupButton');
    const vrm = document.getElementById('vehicle_reg_number').value.trim();
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
        console.log('API Response:', result); // Debugging: Log the API response

        lookupButton.innerHTML = 'Lookup';

        if (response.ok && result.success && result.data) {
            const vehicleDetails = result.data.VehicleDetails?.VehicleIdentification || {};
            const color = result.data.VehicleDetails?.VehicleHistory || {};
            const SmmtDetails = result.data.SmmtDetails?.TechnicalDetails || {};
            const Performance = result.data.SmmtDetails?.Performance || {};
            const tyreDetails = result.data.TyreDetails?.TyreDetailsList?.[0] || {};
            const motHistory = result.data.MotHistoryDetails || {};
            const RapidVehicleDetails = result.data.RapidVehicleDetails?.VehicleClass || {};
            // Populate vehicle details fields
            document.getElementById('vehicle_category').value = RapidVehicleDetails || '';
            document.getElementById('vrm').value = vehicleDetails.Vrm || '';
            document.getElementById('vehicle_make').value = vehicleDetails.DvlaMake || '';
            document.getElementById('vehicle_model').value = vehicleDetails.DvlaModel || '';
            document.getElementById('vehicle_year').value = vehicleDetails.YearOfManufacture || '';
            document.getElementById('vehicle_cc').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('vehicle_fuel_type').value = SmmtDetails.FuelType || '';
            document.getElementById('vehicle_body_type').value = SmmtDetails.BodyStyle || '';
            document.getElementById('vehicle_bhp').value = Performance.PowerBhp || '';
            document.getElementById('vehicle_engine_number').value = vehicleDetails.EngineNumber || '';
            document.getElementById('vehicle_engine_size').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('vehicle_engine_code').value = SmmtDetails.EngineDescription || '';
            document.getElementById('vehicle_vin').value = vehicleDetails.VinLast5 || '';
            document.getElementById('vehicle_front_tyre_size').value = tyreDetails.Front?.Tyre?.SizeDescription || '';
            document.getElementById('vehicle_rear_tyre_size').value = tyreDetails.Rear?.Tyre?.SizeDescription || '';
            document.getElementById('vehicle_colour').value = color?.ColourDetails?.CurrentColour || '';
            document.getElementById('vehicle_first_registered').value = vehicleDetails.DateFirstRegisteredInUk.split("T")[0] || '';
            document.getElementById('vehicle_chassis_no').value = vehicleDetails.Vin || '';
            document.getElementById('vehicle_torque_settings').value = Performance.TorqueNm || '';
            document.getElementById('vehicle_mot_expiry_date').value = motHistory.MotDueDate.split("T")[0] || '';
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
@endsection