<div class="product-tab-list nav pt-50">
    <a class="active" href="#search-by-reg" data-bs-toggle="tab">
        <h4><span>Search by</span>Vehicle Reg</h4>
    </a>
    <a href="#search-by-size" data-bs-toggle="tab">
        <h4><span>Search by</span>Tyre Size</h4>
    </a>
</div>
<div class="tab-content searchengine_style">
    <!-- Form -->
    <div class="tab-pane active" id="search-by-reg">
        <form id="vrmSearchForm" action="/vehicle-data" method="GET">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-12">
                    <div class="reg_outer">
                        <div class="plate_wrap">
                            <img src="frontend/themes/default/img/icon-img/reg_icon.webp" alt="uk icon" width="40"
                                height="40" loading="lazy">
                            <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="reg_number"
                                name="vrm" required>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-12">
                   <select class="form-control" name="ordertype" id="order_type_reg" required>
                        <option value="">Select Order Type</option>
                        @foreach($fittingTypes as $type)
                            <option value="{{ $type->ordertype_name }}" {{ $type->ordertype_name == 'fully_fitted' ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $type->ordertype_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-3 col-12">
                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Postcode"
                        required>
                </div>

                <div class="col-lg-3 col-md-3 col-12">
                    <button type="submit" id="vrm-find-tyres" class="btn btn-theme btn-block">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div class="tab-pane" id="search-by-size">
        <form id="tyreSearchForm" action="{{ route('tyreslist') }}" method="GET">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-12">
                    <select class="form-control" name="vehicletype" id="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        @foreach($vehicleTypes as $vehicleType)
                            <option value="{{ strtolower($vehicleType) }}" {{ strtolower($vehicleType) == 'car' ? 'selected' : '' }}>
                                {{ $vehicleType }}
                            </option>
                        @endforeach
                    </select>

                </div>

                <div class="col-lg-4 col-md-4 col-12">
                    <div class="search_size_area">
                        <div class="column">
                            <select class="form-control" id="car_width" name="width" disabled required>
                                <option value="">Width</option>
                            </select>
                        </div>
                        <div class="column">
                            <select class="form-control" id="car_profile" name="profile" disabled required>
                                <option value="">Profile</option>
                            </select>
                        </div>
                        <div class="column">
                            <select class="form-control" id="car_diameter" name="diameter" disabled required>
                                <option value="">Rim</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-12">
                    <select class="form-control" name="ordertype" id="order_type" required>
                        <option value="">Select Order Type</option>
                        @foreach($fittingTypes as $type)
                            <option value="{{ $type->ordertype_name }}" {{ $type->ordertype_name == 'fully_fitted' ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $type->ordertype_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-12">
                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Postcode"
                        required>
                </div>

                <div class="col-lg-2 col-md-2 col-12">
                    <button type="submit" class="btn btn-theme btn-block" id="search_button">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
    $(document).ready(function() {
        const defaultVehicleType = 'car';
        const defaultWidth = 205;
        const defaultProfile = 55;
        const defaultDiameter = 16;
        const defaultOrderType = 'fully_fitted';

   function loadWidths(vehicleType, widthSelect, defaultValue = null, callback = null) {
        if (vehicleType) {
            $.get('{{ route("tyres.getWidths") }}', { vehicleType: vehicleType }, function(data) {
                $(widthSelect).html(data).prop('disabled', false);
                if (defaultValue) {
                    $(widthSelect).val(defaultValue).trigger('change');
                }
                if (callback) callback();
            });
        }
    }

    function loadProfiles(width, vehicleType, profileSelect, diameterSelect, defaultProfile = null, defaultDiameter = null, callback = null) {
        if (width && vehicleType) {
            $.get('{{ route("tyres.getProfiles") }}', { width: width, vehicleType: vehicleType }, function(data) {
                $(profileSelect).html(data).prop('disabled', false);
                $(diameterSelect).empty().append('<option value="">Select Rim</option>').prop('disabled', true);
                if (defaultProfile) {
                    $(profileSelect).val(defaultProfile).trigger('change');
                    if (defaultDiameter) {
                        setTimeout(() => {
                            loadDiameters(width, defaultProfile, vehicleType, diameterSelect, defaultDiameter);
                        }, 100);
                    }
                }
                if (callback) callback();
            });
        }
    }

    function loadDiameters(width, profile, vehicleType, diameterSelect, defaultDiameter = null) {
        if (width && profile && vehicleType) {
            $.get('{{ route("tyres.getDiameters") }}', { width, profile, vehicleType }, function(data) {
                $(diameterSelect).html(data).prop('disabled', false);
                if (defaultDiameter) {
                    $(diameterSelect).val(defaultDiameter);
                }
            });
        }
    }



        /*function loadOrderTypes(vehicleType, orderTypeSelect, defaultOrderType = null) {
            if (vehicleType) {
                $.get('{{ route("tyres.getOrderTypes") }}', { vehicleType: vehicleType }, function(data) {
                    let options = '<option value="">Select Order Type</option>';
                    if (Array.isArray(data)) {
                        data.forEach(function(type) {
                            options += `<option value="${type}" ${defaultOrderType && defaultOrderType == type ? 'selected' : ''}>${type.replace('_',' ').toUpperCase()}</option>`;
                        });
                    }
                    $(orderTypeSelect).html(options).prop('disabled', false);
                });
            }
        }*/
        function loadOrderTypes(vehicleType, orderTypeSelect, defaultOrderType = null) {
    if (vehicleType) {
        $.get('{{ route("tyres.getOrderTypes") }}', { vehicleType: vehicleType }, function(data) {
            let options = '<option value="">Select Order Type</option>';
            if (Array.isArray(data.order_types)) {
                const firstType = data.order_types[0] || '';
                data.order_types.forEach(function(type) {
                    options += `<option value="${type}" ${firstType === type ? 'selected' : ''}>${type.replace('_',' ').toUpperCase()}</option>`;
                });
            }
            $(orderTypeSelect).html(options).prop('disabled', false);
        });
    }
}


    // Load defaults sequentially
    loadWidths(defaultVehicleType, '#car_width', defaultWidth, function() {
        loadProfiles(defaultWidth, defaultVehicleType, '#car_profile', '#car_diameter', defaultProfile, defaultDiameter);
    });

        $('#vehicle_type').change(function() {
            const vehicleType = $(this).val();
            if (vehicleType) {
                loadWidths(vehicleType, '#car_width');
                $('#car_profile, #car_diameter').empty().append('<option value="">Select</option>').prop('disabled', true);
                loadOrderTypes(vehicleType, '#order_type');
            } else {
                $('#car_width, #car_profile, #car_diameter, #order_type').prop('disabled', true).val('');
            }
        });

        $('#car_width').change(function() {
            const width = $(this).val();
            const vehicleType = $('#vehicle_type').val();
            if (width && vehicleType) {
                loadProfiles(width, vehicleType, '#car_profile', '#car_diameter');
            } else {
                $('#car_profile, #car_diameter').empty().append('<option value="">Select</option>').prop('disabled', true);
            }
        });

        $('#car_profile').change(function() {
            const width = $('#car_width').val();
            const profile = $(this).val();
            const vehicleType = $('#vehicle_type').val();
            if (width && profile && vehicleType) {
                loadDiameters(width, profile, vehicleType, '#car_diameter');
            } else {
                $('#car_diameter').empty().append('<option value="">Select</option>').prop('disabled', true);
            }
        });

        $('#car_diameter').change(function() {
            const diameter = $(this).val();
            $('#order_type').prop('disabled', !diameter);
        });

        $('#postcode').on('input', function() {
            $('#search_button').prop('disabled', $(this).val().trim().length === 0);
        });

    });
</script>
@endpush