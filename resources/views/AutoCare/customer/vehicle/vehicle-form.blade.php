@extends('samples')

@section('content')

    <div class="container-fluid">
        <div class="bg-white p-3">
            @include('AutoCare.customer.menu')
            <div class="short__item">
                <div class="bg-light p-2 text-center rounded mb-4 border">
                    <h5 class="m-0"><strong>{{ isset($vehicle) ? 'Edit Vehicle' : 'Add Vehicle' }}</strong></h5>
                </div>
                <form action="{{ isset($vehicle)
        ? route('AutoCare.customer.vehicles.update', ['id' => $customer->id, 'vehicleId' => $vehicle->id])
        : route('AutoCare.customer.vehicles.store', ['id' => $customer->id]) }}" method="POST">
                    @csrf
                    @if (isset($vehicle))
                        @method('PUT')
                    @endif
                    <div class="edit_vehicle_plate mb-4">
                        <div class="plate_wrap">
                            <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" height="48">
                            <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="vehicle_reg_number" name="vrm">
                        </div>
                        <button type="button" id="lookupButton" class="btn btn-primary px-4">Get Vehicle
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
                                <input type="text" name="vehicle_make" id="make" class="form-control"
                                    value="{{ old('make', $vehicle->vehicle_make ?? '') }}">
                                @error('make')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Model</label>
                                <input type="text" name="vehicle_model" id="model" class="form-control"
                                    value="{{ old('model', $vehicle->vehicle_model ?? '') }}">
                                @error('model')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="text" name="vehicle_year" id="year" class="form-control"
                                    value="{{ old('year', $vehicle->vehicle_year ?? '') }}">
                                @error('year')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>CC</label>
                                <input type="text" name="vehicle_cc" id="cc" class="form-control"
                                    value="{{ old('cc', $vehicle->vehicle_cc ?? '') }}">
                                @error('cc')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Fuel Type</label>
                                <input type="text" name="vehicle_fuel_type" id="fuel_type" class="form-control"
                                    value="{{ old('fuel_type', $vehicle->vehicle_fuel_type ?? '') }}">
                                @error('fuel_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Body Type</label>
                                <input type="text" name="vehicle_body_type" id="body_type" class="form-control"
                                    value="{{ old('body_type', $vehicle->vehicle_body_type ?? '') }}">
                                @error('body_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>BHP</label>
                                <input type="text" name="vehicle_bhp" id="bhp" class="form-control"
                                    value="{{ old('bhp', $vehicle->vehicle_bhp ?? '') }}">
                                @error('bhp')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Number</label>
                                <input type="text" name="vehicle_engine_number" id="engine_number" class="form-control"
                                    value="{{ old('engine_number', $vehicle->vehicle_engine_number ?? '') }}">
                                @error('engine_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Size</label>
                                <input type="text" name="vehicle_engine_size" id="engine_size" class="form-control"
                                    value="{{ old('engine_size', $vehicle->vehicle_engine_size ?? '') }}">
                                @error('engine_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Engine Code</label>
                                <input type="text" name="vehicle_engine_code" id="engine_code" class="form-control"
                                    value="{{ old('engine_code', $vehicle->vehicle_engine_code ?? '') }}">
                                @error('engine_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>VIN</label>
                                <input type="text" name="vehicle_vin" id="vin" class="form-control"
                                    value="{{ old('vin', $vehicle->vehicle_vin ?? '') }}">
                                @error('vin')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Front Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_front_tyre_size" id="front_tyre_size" class="form-control"
                                    value="{{ old('front_tyre_size', $vehicle->vehicle_front_tyre_size ?? '') }}">
                                @error('front_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Rear Tyre Size <small>(205/55R16 91W)</small></label>
                                <input type="text" name="vehicle_rear_tyre_size" id="rear_tyre_size" class="form-control"
                                    value="{{ old('rear_tyre_size', $vehicle->vehicle_rear_tyre_size ?? '') }}">
                                @error('rear_tyre_size')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Colour</label>
                                <input type="text" name="vehicle_colour" id="colour" class="form-control"
                                    value="{{ old('colour', $vehicle->vehicle_colour ?? '') }}">
                                @error('colour')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>First Registered</label>
                                <input type="date" name="vehicle_first_registered" id="first_registered" class="form-control"
                                    value="{{ old('first_registered', isset($vehicle->vehicle_first_registered) ? \Carbon\Carbon::parse($vehicle->vehicle_first_registered)->format('Y-m-d') : '') }}">

                                @error('first_registered')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Chassis No.</label>
                                <input type="text" name="vehicle_chassis_no" id="chassis_no" class="form-control"
                                    value="{{ old('chassis_no', $vehicle->vehicle_chassis_no ?? '') }}">
                                @error('chassis_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>Torque Settings</label>
                                <input type="text" name="vehicle_torque_settings" id="torque_settings" class="form-control"
                                    value="{{ old('torque_settings', $vehicle->vehicle_torque_settings ?? '') }}">
                                @error('torque_settings')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-12">
                            <div class="form-group">
                                <label>MOT Expiry Date</label>
                                <input type="date" name="vehicle_mot_expiry_date" id="mot_expiry_date" class="form-control"
                                    value="{{ old('mot_expiry_date', $vehicle->vehicle_mot_expiry_date ?? '') }}">
                                @error('mot_expiry_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit"
                            class="btn btn-primary">{{ isset($vehicle) ? 'Update Vehicle' : 'Add Vehicle' }}</button>
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
            document.getElementById('make').value = vehicleDetails.DvlaMake || '';
            document.getElementById('model').value = vehicleDetails.DvlaModel || '';
            document.getElementById('year').value = vehicleDetails.YearOfManufacture || '';
            document.getElementById('cc').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('fuel_type').value = SmmtDetails.FuelType || '';
            document.getElementById('body_type').value = SmmtDetails.BodyStyle || '';
            document.getElementById('bhp').value = Performance.PowerBhp || '';
            document.getElementById('engine_number').value = vehicleDetails.EngineNumber || '';
            document.getElementById('engine_size').value = SmmtDetails.EngineCapacityCc || '';
            document.getElementById('engine_code').value = SmmtDetails.EngineDescription || '';
            document.getElementById('vin').value = vehicleDetails.VinLast5 || '';
            document.getElementById('front_tyre_size').value = tyreDetails.Front?.Tyre?.SizeDescription || '';
            document.getElementById('rear_tyre_size').value = tyreDetails.Rear?.Tyre?.SizeDescription || '';
            document.getElementById('colour').value = color?.ColourDetails?.CurrentColour || '';
            document.getElementById('first_registered').value = vehicleDetails.DateFirstRegisteredInUk.split("T")[0] || '';
            document.getElementById('chassis_no').value = vehicleDetails.Vin || '';
            document.getElementById('torque_settings').value = Performance.TorqueNm || '';
            document.getElementById('mot_expiry_date').value = motHistory.MotDueDate.split("T")[0] || '';
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