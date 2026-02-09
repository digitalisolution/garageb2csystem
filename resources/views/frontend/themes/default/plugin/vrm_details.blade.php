@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @if (!empty($result['success']) && !empty($result['data']))
        @php
            $vehicle = $result['data']['RapidVehicleDetails'] ?? [];
            $tyreData = $result['data']['TyreDetails']['TyreDetailsList'][0] ?? [];
        @endphp
        <div class="col-lg-5 m-auto">
            <div class="bg-gray p-5 border rounded">
                <div class="text-center">
                <div class="mb-3"><i class="fa fa-car fa-4x"></i></div>
                <h4>Vehicle Details for VRM: <span class="text-uppercase vrm-plate">{{ $vrm }}</span></h4>
                <strong>Make:</strong> {{ $vehicle['Make'] ?? 'N/A' }}, 
                <strong>Model:</strong> {{ $vehicle['Model'] ?? 'N/A' }}, <br>
                <strong>Year:</strong> 
                        {{ \Carbon\Carbon::parse($vehicle['DateOfFirstRegistration'] ?? null)->format('Y') ?: 'N/A' }}, 
                <strong>Engine CC:</strong> {{ $vehicle['EngineCapacityCc'] ?? 'N/A' }}
                </div>
                @if($tyreSizes && $tyreSizes->isNotEmpty())
                <div class="bg-white p-4 mt-3 rounded border">
                <form id="tyreSearchForm" action="{{ route('tyreslist') }}" method="GET">
                    <input type="hidden" name="vrm" value="{{ $vrm }}">
                    <div class="form-group mb-3">
                        <label for="tyre_size">Select Tyre Size</label>
                        <select name="tyre_size" id="tyre_size" class="form-control" required>
                            <option value="">-- Select Tyre Size --</option>
                            @foreach ($tyreSizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="width" id="width">
                    <input type="hidden" name="profile" id="profile">
                    <input type="hidden" name="diameter" id="diameter">
                    <input type="hidden" id ="vrm" name="vrm" value="{{ $vrm }}">
                    <input type="hidden" id="car_make" value="{{ $vehicle['Make'] ?? '' }}">
                    <input type="hidden" id="car_model" value="{{ $vehicle['Model'] ?? '' }}">
                    <input type="hidden" id="car_year" value="{{ \Carbon\Carbon::parse($vehicle['DateOfFirstRegistration'] ?? null)->format('Y') ?? '' }}">
                    <input type="hidden" id="car_engine" value="{{ $vehicle['EngineCapacityCc'] ?? '' }}">
                    <input type="hidden" name="fitting_type" id="fitting_type" value="{{ $fitting_type }}">
                    <button type="submit" class="btn btn-theme btn-block">Find Matching Tyres</button>
                </form>
                </div>
                @else
                 <form id="serviceSearchplugin" action="{{ route('service') }}" method="GET">
                    <input type="hidden" id ="vrm" name="vrm" value="{{ $vrm }}">
                    <input type="hidden" id="car_make" value="{{ $vehicle['Make'] ?? '' }}">
                    <input type="hidden" id="car_model" value="{{ $vehicle['Model'] ?? '' }}">
                    <input type="hidden" id="car_year" value="{{ \Carbon\Carbon::parse($vehicle['DateOfFirstRegistration'] ?? null)->format('Y') ?? '' }}">
                    <input type="hidden" id="car_engine" value="{{ $vehicle['EngineCapacityCc'] ?? '' }}">
                    <button type="submit" class="btn btn-theme btn-block">Search Services</button>
                </form>
                @endif
            </div>
            <div class="text-right"><a href="{{ route('plugin.search.form') }}" class="btn btn-default border mt-3">Back to Search</a></div>
        </div>

    @else
        <div class="alert alert-danger">No data found for VRM: <strong>{{ $vrm }}</strong></div>
    @endif

    
</div>
@endsection

@push('scripts')

<style type="text/css">
.vrm-plate{background:#ffcc00;border:solid 2px rgba(0,0,0,0.3);border-radius:6px;color:#000;padding:2px 10px;}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tyreSizeSelect = document.getElementById('tyre_size');
    const fittingTypeInputs = document.querySelectorAll('input[name="fitting_type"]');
    // let selectedFittingType = 'fully_fitted';

    // fittingTypeInputs.forEach(input => {
    //     if (input.checked) selectedFittingType = input.value;

    //     input.addEventListener('change', function () {
    //         selectedFittingType = this.value;
    //     });
    // });
if(tyreSizeSelect){
    tyreSizeSelect.addEventListener('change', function () {
        const sizeStr = this.value;

        if (!sizeStr) return;

        // Parse into parts
        const [widthProfilePart, diameter] = sizeStr.split('R');
        const [width, profile] = widthProfilePart.split('/');

        document.getElementById('width').value = width || '';
        document.getElementById('profile').value = profile || '';
        document.getElementById('diameter').value = diameter || '';
        document.querySelectorAll('input[name="fitting_type"]').value = fittingTypeInputs;
    });
}
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tyreForm = document.getElementById('tyreSearchForm');
    const serviceForm = document.getElementById('serviceSearchplugin');
    const CarServicePlugin = document.getElementById('CarServicePlugin');

    // Common vehicle data fields
    const regNumber = document.getElementById('vrm')?.value;
    const make = document.getElementById('car_make')?.value;
    const model = document.getElementById('car_model')?.value;
    const year = document.getElementById('car_year')?.value;
    const engine = document.getElementById('car_engine')?.value;

    const vehicleData = { regNumber, make, model, year, engine };

    async function storeVehicleData() {
        try {
            const response = await fetch('/store-vehicle-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(vehicleData),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                alert(result.message || 'Failed to store vehicle data.');
            }
        } catch (error) {
            console.error('Error storing vehicle data:', error.message);
            alert('An error occurred while storing vehicle data.');
        }
    }

    if (tyreForm) {
        tyreForm.addEventListener('submit', async function (e) {
            await storeVehicleData();
        });
    }

     if (serviceForm) {
        serviceForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            await storeVehicleData();
            window.location.href = serviceForm.action;
        });
    }
});

</script>
@endpush