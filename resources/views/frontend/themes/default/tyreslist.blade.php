@extends('layouts.app')
@section('content')
    <div class="breadcrumb-area pt-20 pb-20 bg-gray-3">
        <div class="container">
            <div class="d-flex align-items-center flex-wrap justify-content-center">
                <div class="breadcrumb-content text-center">
                    <h1 id="breadcrumbText"></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="shop-area pt-30 pb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="product-filter-wrapper">
                        <h3 class="mb-20">Filter</h3>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button" data-bs-toggle="collapse" data-bs-target="#tyre-size"
                                        aria-expanded="true" aria-controls="tyre-size">
                                        <h4><img src="frontend/themes/default/img/filter-icons/tyresize_icon.webp"
                                                height="18" alt="Tyre Sizes"> Tyre Sizes</h4>
                                    </a>
                                </h2>
                                <div id="tyre-size" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <form id="filterForm">
                                            <div class="filter_element">
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Vehicle Type</h5>
                                                    </div>
                                                    <select class="form-control capitalize" id="vehicleTypeSelect">
                                                        <option value="">Any Vehicle Type</option>
                                                    </select>
                                                </div>
                                                <div class="item col-lg-12">
                                                    <div class="product-filter">
                                                        <h5>Order Type</h5>
                                                    </div>
                                                    <select class="form-control" id="orderTypeSelect">
                                                        <option value="">Any Order Type</option>
                                                    </select>
                                                    <input type="hidden" id="ordertype" name="ordertype"
                                                        value="{{ session('user_ordertype', '') }}">
                                                </div>
                                                <input type="hidden" id="vehicle_type" name="vehicle_type"
                                                    value="{{ request('vehicle_type', '') }}">
                                                <!-- <input type="hidden" id="ordertype_hidden" name="ordertype"
                                                    value="{{ session('user_ordertype', '') }}"> -->
                                                <!-- Width -->
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Width</h5>
                                                    </div>
                                                    <select name="width" id="width" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($widths as $width)
                                                            <option value="{{ $width }}">{{ $width }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Profile -->
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Profile</h5>
                                                    </div>
                                                    <select name="profile" id="profile" class="form-control" disabled>
                                                        <option value="">Select Profile</option>
                                                    </select>
                                                </div>
                                                <!-- Diameter -->
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Diameter</h5>
                                                    </div>
                                                    <select name="diameter" id="diameter" class="form-control" disabled>
                                                        <option value="">Select Diameter</option>
                                                    </select>
                                                </div>
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Speed Index</h5>
                                                    </div>
                                                    <select class="form-control" id="speedIndexSelect">
                                                        <option value="">Any Speed</option>
                                                    </select>
                                                </div>
                                                <div class="item">
                                                    <div class="product-filter">
                                                        <h5>Load Rating</h5>
                                                    </div>
                                                    <select class="form-control" id="loadIndexSelect">
                                                        <option value="">Any Load Rating</option>
                                                    </select>
                                                </div>
                                                <input type="hidden" id="load_index" name="load_index"
                                                    value="{{ request('load_index', '') }}">
                                                <input type="hidden" id="speed_index" name="speed_index"
                                                    value="{{ request('speed_index', '') }}">

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#tyres-brand" aria-expanded="false" aria-controls="tyres-brand">
                                        <h4><img src="frontend/themes/default/img/filter-icons/tyrebrands_icon.webp"
                                                height="18" alt="Tyres Brand"> Tyres Brand</h4>
                                    </a>
                                </h2>
                                <div id="tyres-brand" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter brand_filter">
                                            <ul class="color-filter" id="tyreBrand">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#season"
                                        aria-expanded="false" aria-controls="season">
                                        <h4><img src="frontend/themes/default/img/filter-icons/season_icon.webp" height="18"
                                                alt="Season"> Season</h4>
                                    </a>
                                </h2>
                                <div id="season" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter">
                                            <ul class="color-filter" id="getSeason">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#application" aria-expanded="false" aria-controls="application">
                                        <h4><img src="frontend/themes/default/img/filter-icons/application_icon.webp"
                                                height="18" alt="Application"> Application</h4>
                                    </a>
                                </h2>
                                <div id="application" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter">
                                            <ul class="color-filter">
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="runflat" value="1"
                                                            id="runflatCheckbox">
                                                        <a class="filter-label">Run Flat (RFT)</a>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="extraload" value="1"
                                                            id="extraloadCheckbox">
                                                        <a class="filter-label">Reinforced (XL)</a>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#fuel-efficiency" aria-expanded="false"
                                        aria-controls="fuel-efficiency">
                                        <h4><img src="frontend/themes/default/img/filter-icons/fuel_icon.webp" height="18"
                                                alt="Fuel Efficiency"> Fuel Efficiency</h4>
                                    </a>
                                </h2>
                                <div id="fuel-efficiency" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter">
                                            <ul class="color-filter" id="fuelFilter">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#wet-grip" aria-expanded="false" aria-controls="wet-grip">
                                        <h4><img src="frontend/themes/default/img/filter-icons/wetgrip_icon.webp"
                                                height="18" alt="Wet Grip"> Wet Grip</h4>
                                    </a>
                                </h2>
                                <div id="wet-grip" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter">
                                            <ul class="color-filter" id="wetGrip">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a class="accordion-button collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#noise-level" aria-expanded="false" aria-controls="noise-level">
                                        <h4><img src="frontend/themes/default/img/filter-icons/noise_icon.webp" height="18"
                                                alt="Noise Level (dB)"> Noise Level (dB)</h4>
                                    </a>
                                </h2>
                                <div id="noise-level" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="product-filter">
                                            <ul class="color-filter" id="noiseLevelFilter">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    @if($recommendedTyres->isNotEmpty())
                        <div class="shop-area mb-40">
                            <h3 class="mb-20">Recommended Tyres</h3>
                            <div id="recommendedTyreList" class="tyrelist_repeater">
                                @include('recommended-tyres', ['recommendedTyres' => $recommendedTyres, 'message' => $message])
                            </div>
                        </div>
                    @endif
                    <div class="shop-bottom-area">
                        <div id="tyreList">
                            @include('tyre-cards', ['tyres' => $tyres])
                        </div>
                        @if ($tyres->hasPages())
                            <div class="text-center mt-30">
                                <button id="loadMoreBtn" class="btn btn-theme" data-page="2">Load More</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Postcode Modal -->
    <div id="postcodeModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mobile_popup_content">
                <div class="mobile_popup text-center">
                    <img src="frontend/themes/default/img/icon-img/mobile-van.png" alt="mobile van">
                    <span class="bottom_arrow"></span>
                </div>
                <!-- Modal Body -->
                <div class="modal-body text-center">
                    <h4>Enter Your Postcode</h4>
                    <form id="postcodeForm" action="submit-postcode" method="POST">
                        <div class="col-lg-8 m-auto text-center">
                            <input type="text" value="{{ session('user_postcode')  }}" class="form-control mb-2"
                                name="postcode" id="postcode" maxlength="8" placeholder="ENTER POSTCODE" required>
                            <button type="submit" class="btn btn-theme btn-block">Submit</button>
                        </div>
                    </form>

                    <div class="mt-2">
                        <span id="postcodeErrorMsg" class="text-danger"></span>
                    </div>

                    <div id="resultMessage" class="mt-3"></div>
                    <div class="mt-3" id="actionButtons">
                        <button id="continueButton" class="btn btn-success me-2" style="display: none;">Continue</button>
                        <button id="changePostcodeButton" class="btn btn-secondary" style="display: none;">Change
                            Postcode</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="mailorderpostcodeModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mobile_popup_content">
                <div class="mobile_popup text-center">
                    <img src="frontend/themes/default/img/icon-img/mobile-van.png" alt="mobile van">
                    <span class="bottom_arrow"></span>
                </div>
                <div class="modal-body text-center">
                    <h4>Enter Your Postcode</h4>
                    <form id="mailorderpostcodeForm" action="/submit-postcode" method="POST">
                        <div class="col-lg-8 m-auto text-center">
                            <input type="text" value="{{ session('user_postcode')  }}" class="form-control mb-2"
                                name="postcode" id="postcode" maxlength="8" placeholder="ENTER POSTCODE" required>
                            <button type="submit" class="btn btn-theme btn-block">Submit</button>
                        </div>
                    </form>
                    <div class="mt-2">
                        <span id="postcodeErrorMsg" class="text-danger"></span>
                    </div>
                    <div id="resultMailorderMessage" class="mt-3"></div>
                    <div class="mt-3" id="actionButtons">
                        <button id="continueMailorderButton" class="btn btn-success me-2"
                            style="display: none;">Continue</button>
                        <button id="changeMailorderPostcodeButton" class="btn btn-secondary" style="display: none;">Change
                            Postcode</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Include jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
    <script>
        let selectedLoadIndex = $('#load_index').val();
        let selectedSpeedIndex = $('#speed_index').val();
        let selectedVehicleType = $('#vehicle_type').val();
        let selectedOrderType = $('#ordertype').val();

        function applyFilters() {
            const width = $('#width').val();
            const profile = $('#profile').val();
            const diameter = $('#diameter').val();
            const loadIndex = selectedLoadIndex;
            const speedIndex = selectedSpeedIndex;
            const vehicleType = selectedVehicleType;
            const orderType = selectedOrderType;
            const fuelEfficiency = $('input[name="fuel[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const getSeason = $('input[name="season[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const getWetGrip = $('input[name="wetGrip[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const getTyreBrand = $('input[name="tyreBrand[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const noiseLevel = $('input[name="noiseLevel[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const runflat = $('#runflatCheckbox').is(':checked') ? $('#runflatCheckbox').val() : null;
            const extraload = $('#extraloadCheckbox').is(':checked') ? $('#extraloadCheckbox').val() : null;

            $.ajax({
                url: "{{ route('tyres.filter') }}",
                type: "GET",
                data: {
                    width: width,
                    profile: profile,
                    diameter: diameter,
                    load_index: loadIndex,
                    speed_index: speedIndex,
                    fuel: fuelEfficiency,
                    season: getSeason,
                    wetGrip: getWetGrip,
                    tyreBrand: getTyreBrand,
                    runflat: runflat,
                    extraload: extraload,
                    vehicle_type: vehicleType,
                    ordertype: orderType,
                    noiseLevel: noiseLevel,
                },
                success: function (response) {
                    $('#tyreList').html(response.tyres);
                    if (response.recommendedTyres && response.recommendedTyres.trim() !== '') {
                        $('#recommendedTyreList').html(response.recommendedTyres).closest('.shop-area').show();
                    } else {
                        $('#recommendedTyreList').closest('.shop-area').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    alert("An error occurred while filtering tyres.");
                }
            });
        }
        function loadOrderTypesByVehicleType(vehicleType) {
            $.ajax({
                url: "{{ route('tyres.getOrderTypesOptions') }}",
                type: "GET",
                data: { vehicleType: vehicleType },
                success: function (data) {
                    const $orderTypeSelect = $('#orderTypeSelect');
                    $orderTypeSelect.empty();
                    $orderTypeSelect.append(new Option('Any Order Type', ''));
                    let firstValue = @json(session('user_ordertype', ''));
                    let availableOrderTypes = [];

                    if (Array.isArray(data) && data.length > 0) {
                        $.each(data, function (index, orderType) {
                            let value = orderType.ordertype_name || orderType;
                            if (!value) return;

                            let displayName = value
                                .replace(/_/g, ' ')
                                .replace(/\w\S*/g, txt => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());

                            $orderTypeSelect.append(new Option(displayName, value));
                            availableOrderTypes.push(value);
                        });

                        if (!firstValue || !availableOrderTypes.includes(firstValue)) {
                            firstValue = availableOrderTypes[0];
                        }
                    }
                    if (firstValue) {
                        $orderTypeSelect.val(firstValue);
                        selectedOrderType = firstValue;
                        $('#ordertype').val(firstValue);
                        updateURLParams('fitting_type', firstValue);
                    } else {
                        selectedOrderType = '';
                        $('#ordertype').val('');
                    }

                    attachOrderTypeListener();
                    const postcodeData = {!! json_encode(session('postcode_data')) !!};
                    const user_postcode = {!! json_encode(session('user_postcode')) !!};
                    if (firstValue === 'mailorder' && (!postcodeData || !user_postcode)) {
                        const mailorderModal = new bootstrap.Modal(document.getElementById('mailorderpostcodeModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        mailorderModal.show();
                    } else if (firstValue === 'mobile_fitted' && (!postcodeData || !user_postcode)) {
                        const mobileModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        mobileModal.show();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Order Types:", error);
                    $('#orderTypeSelect').empty().append('<option value="">Any Order Type</option>');
                    attachOrderTypeListener();
                }
            });
        }
        function loadFilterOptions() {
            // Fuel Efficiency
            $.ajax({
                url: "{{ route('tyres.getFuelEfficiencyOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#fuelFilter').empty();
                    $.each(data, function (index, fuelEfficiency) {
                        $('#fuelFilter').append(`
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="fuel[]" value="${fuelEfficiency}">
                                                        <a>${fuelEfficiency}</a>
                                                    </label>
                                                </li>
                                            `);
                    });
                    $('#fuelFilter input[type="checkbox"]').off('change.filter').on('change.filter', applyFilters);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
            // Season
            $.ajax({
                url: "{{ route('tyres.getSeasonOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#getSeason').empty();
                    $.each(data, function (index, seasons) {
                        $('#getSeason').append(`
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="season[]" value="${seasons}">
                                                        <a>${seasons}</a>
                                                    </label>
                                                </li>
                                            `);
                    });
                    $('#getSeason input[type="checkbox"]').off('change.filter').on('change.filter', applyFilters);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
            // Wet Grip
            $.ajax({
                url: "{{ route('tyres.getWetGripOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#wetGrip').empty();
                    $.each(data, function (index, wetGripOption) {
                        $('#wetGrip').append(`
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="wetGrip[]" value="${wetGripOption}">
                                                        <a>${wetGripOption}</a>
                                                    </label>
                                                </li>
                                            `);
                    });
                    $('#wetGrip input[type="checkbox"]').off('change.filter').on('change.filter', applyFilters);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
            // Tyre Brand
            $.ajax({
                url: "{{ route('tyres.getTyreBrandOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#tyreBrand').empty();
                    $.each(data, function (index, brand) {
                        $('#tyreBrand').append(`
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="tyreBrand[]" value="${brand.brand_id}">
                                                        <a>${brand.name}</a>
                                                    </label>
                                                </li>
                                            `);
                    });
                    $('#tyreBrand input[type="checkbox"]').off('change.filter').on('change.filter', applyFilters);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
            // Load Index
            $.ajax({
                url: "{{ route('tyres.getLoadIndexOptions') }}",
                type: "GET",
                success: function (data) {
                    const $loadIndexSelect = $('#loadIndexSelect');
                    $loadIndexSelect.empty();
                    $loadIndexSelect.append(new Option('Any Load Index', ''));
                    data.sort((a, b) => a - b);
                    $.each(data, function (index, loadIndexValue) {
                        const isSelected = String(loadIndexValue) === selectedLoadIndex;
                        $loadIndexSelect.append(new Option(loadIndexValue, loadIndexValue, false, isSelected));
                    });
                    attachLoadIndexListener();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Load Index options:", error);
                }
            });
            // Speed Index
            $.ajax({
                url: "{{ route('tyres.getSpeedIndexOptions') }}",
                type: "GET",
                success: function (data) {
                    const $speedIndexSelect = $('#speedIndexSelect');
                    $speedIndexSelect.empty();
                    $speedIndexSelect.append(new Option('Any Speed Index', ''));
                    data.sort();
                    $.each(data, function (index, speedIndexValue) {
                        const isSelected = speedIndexValue === selectedSpeedIndex;
                        $speedIndexSelect.append(new Option(speedIndexValue, speedIndexValue, false, isSelected));
                    });
                    attachSpeedIndexListener();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Speed Index options:", error);
                }
            });
            // Vehicle Type
            $.ajax({
                url: "{{ route('tyres.getVehicleTypeOptions') }}",
                type: "GET",
                success: function (data) {
                    const $vehicleTypeSelect = $('#vehicleTypeSelect');
                    $vehicleTypeSelect.empty();
                    $vehicleTypeSelect.append(new Option('Any Vehicle Type', ''));
                    $.each(data, function (index, vehicleTypeValue) {
                        const isSelected = vehicleTypeValue === selectedVehicleType;
                        $vehicleTypeSelect.append(new Option(vehicleTypeValue, vehicleTypeValue, false, isSelected));
                    });
                    attachVehicleTypeListener();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Vehicle Type options:", error);
                }
            });

            $.ajax({
                url: "{{ route('tyres.getNoiseLevelOptions') }}",
                type: "GET",
                success: function (data) {
                    const $noiseLevelFilter = $('#noiseLevelFilter');
                    $noiseLevelFilter.empty();
                    data.sort((a, b) => a - b);
                    $.each(data, function (index, noiseLevel) {
                        $noiseLevelFilter.append(`
                                                <li>
                                                    <label>
                                                        <input type="checkbox" name="noiseLevel[]" value="${noiseLevel}">
                                                        <a>${noiseLevel}db</a>
                                                    </label>
                                                </li>
                                            `);
                    });
                    $('#noiseLevelFilter input[type="checkbox"]').off('change.filter').on('change.filter', applyFilters);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching Noise Level options:", error);
                }
            });
        }

        function attachLoadIndexListener() {
            $('#loadIndexSelect').off('change.loadIndexFilter').on('change.loadIndexFilter', function () {
                selectedLoadIndex = $(this).val();
                $('#load_index').val(selectedLoadIndex);
                updateURLParams('load_index', selectedLoadIndex);
                applyFilters();
            });
        }
        function attachSpeedIndexListener() {
            $('#speedIndexSelect').off('change.speedIndexFilter').on('change.speedIndexFilter', function () {
                selectedSpeedIndex = $(this).val();
                $('#speed_index').val(selectedSpeedIndex);
                updateURLParams('speed_index', selectedSpeedIndex);
                applyFilters();
            });
        }
        function attachVehicleTypeListener() {
            $('#vehicleTypeSelect').off('change.vehicleTypeFilter').on('change.vehicleTypeFilter', function () {
                const newVehicleType = $(this).val();

                // Only reset if the value actually changed (not on initial load)
                if (newVehicleType !== selectedVehicleType) {
                    // Reset all other filters
                    resetAllFiltersExceptVehicleType(newVehicleType);
                }

                selectedVehicleType = newVehicleType;
                $('#vehicle_type').val(selectedVehicleType);
                updateURLParams('vehicle_type', selectedVehicleType);
                updateBreadcrumb();

                // Apply filters after reset
                applyFilters();
            });
        }
        function attachOrderTypeListener() {
            $('#orderTypeSelect').off('change.orderTypeFilter').on('change.orderTypeFilter', function () {
                const newOrderType = $(this).val();
                selectedOrderType = newOrderType;
                $('#ordertype').val(selectedOrderType);
                updateURLParams('fitting_type', selectedOrderType);
                updateBreadcrumb();
                const postcodeData = {!! json_encode(session('postcode_data')) !!};
                const user_postcode = {!! json_encode(session('user_postcode')) !!};

                if (newOrderType === 'mobile_fitted' && (!postcodeData || !user_postcode)) {
                    const postcodeModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    postcodeModal.show();
                    return;
                }

                if (newOrderType === 'mailorder' && (!postcodeData || !user_postcode)) {
                    const mailorderpostcodeModal = new bootstrap.Modal(document.getElementById('mailorderpostcodeModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    mailorderpostcodeModal.show();
                    return;
                }
                applyFilters();
            });
        }
        $('#runflatCheckbox, #extraloadCheckbox').off('change.filter').on('change.filter', function () {
            applyFilters();
        });
        loadFilterOptions();
        $('#width').off('change.sizeFilter').on('change.sizeFilter', function () {
            const width = $(this).val();
            const urlParams = new URLSearchParams(window.location.search);
            const vehicleTypeFromUrl = urlParams.get('vehicle_type');
            updateURLParams('width', width);
            $('#profile').prop('disabled', true).html('<option value="">Select Profile</option>');
            $('#diameter').prop('disabled', true).html('<option value="">Select Diameter</option>');
            if (width) {
                $.ajax({
                    url: "{{ route('tyres.getProfiles') }}",
                    type: "GET",
                    data: { width: width, vehicleType: vehicleTypeFromUrl },
                    success: function (data) {
                        $('#profile').prop('disabled', false).html(data);
                        const currentProfile = new URLSearchParams(window.location.search).get('profile');
                        if (currentProfile) {
                            $('#profile').val(currentProfile);
                            if ($('#profile').val()) {
                                $('#profile').trigger('change');
                            }
                        }
                    },
                });
            } else {
                $('#profile').html('<option value="">Select Profile</option>');
                $('#diameter').html('<option value="">Select Diameter</option>');
                applyFilters();
            }
        });

        $('#profile').off('change.sizeFilter').on('change.sizeFilter', function () {
            const profile = $(this).val();
            const urlParams = new URLSearchParams(window.location.search);
            const vehicleTypeFromUrl = urlParams.get('vehicle_type');
            updateURLParams('profile', profile);
            $('#diameter').prop('disabled', true).html('<option value="">Select Diameter</option>');
            if (profile) {
                const width = $('#width').val();
                $.ajax({
                    url: "{{ route('tyres.getDiameters') }}",
                    type: "GET",
                    data: { width: width, profile: profile, vehicleType: vehicleTypeFromUrl },
                    success: function (data) {
                        $('#diameter').prop('disabled', false).html(data);
                        const currentDiameter = new URLSearchParams(window.location.search).get('diameter');
                        if (currentDiameter) {
                            $('#diameter').val(currentDiameter);
                            if ($('#diameter').val()) {
                                applyFilters();
                            }
                        }
                    },
                });
            } else {
                $('#diameter').html('<option value="">Select Diameter</option>');
                if ($('#width').val()) {
                    applyFilters();
                }
            }
        });

        $('#diameter').off('change.sizeFilter').on('change.sizeFilter', function () {
            const diameter = $(this).val();
            updateURLParams('diameter', diameter);
            applyFilters();
        });

        function updateURLParams(param, value) {
            const urlParams = new URLSearchParams(window.location.search);
            if (value) {
                urlParams.set(param, value);
            } else {
                urlParams.delete(param);
            }
            const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
            window.history.replaceState({ path: newUrl }, '', newUrl);
            updateBreadcrumb();
        }

        function updateBreadcrumb() {
            const urlParams = new URLSearchParams(window.location.search);
            const width = urlParams.get('width');
            const profile = urlParams.get('profile');
            const diameter = urlParams.get('diameter');
            const loadIndex = selectedLoadIndex || urlParams.get('load_index');
            const speedIndex = selectedSpeedIndex || urlParams.get('speed_index');
            let breadcrumbText = 'Tyre Size: ';
            if (width) {
                breadcrumbText += `${width}`;
            }
            if (profile) {
                breadcrumbText += `/${profile}`;
            }
            if (diameter) {
                breadcrumbText += `R${diameter}`;
            }
            if (loadIndex) {
                breadcrumbText += ` ${loadIndex}`;
            }
            if (speedIndex) {
                breadcrumbText += ` ${speedIndex}`;
            }

            document.getElementById('breadcrumbText').textContent = breadcrumbText;
        }

        $(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const width = urlParams.get('width');
            const profile = urlParams.get('profile');
            const diameter = urlParams.get('diameter');
            const loadIndexFromUrl = urlParams.get('load_index');
            const speedIndexFromUrl = urlParams.get('speed_index');
            const initialVehicleType = urlParams.get('vehicle_type') || 'car';
            const orderTypeFromUrl = urlParams.get('fitting_type');
            const vehicleTypeFromUrl = urlParams.get('vehicle_type');

            if (loadIndexFromUrl !== null) {
                selectedLoadIndex = loadIndexFromUrl;
                $('#load_index').val(loadIndexFromUrl);
            }
            if (speedIndexFromUrl !== null) {
                selectedSpeedIndex = speedIndexFromUrl;
                $('#speed_index').val(speedIndexFromUrl);
            }
            if (vehicleTypeFromUrl !== null) {
                selectedVehicleType = vehicleTypeFromUrl;
                $('#vehicle_type').val(vehicleTypeFromUrl);
                loadOrderTypesByVehicleType(initialVehicleType);
            }
            if (orderTypeFromUrl !== null) {
                selectedOrderType = orderTypeFromUrl;
                $('#ordertype').val(orderTypeFromUrl);
            }

            if (width) {
                $('#width').val(width);
                $('#profile').prop('disabled', false);
                $.ajax({
                    url: "{{ route('tyres.getProfiles') }}",
                    type: "GET",
                    data: { width: width, vehicleType: vehicleTypeFromUrl },
                    success: function (data) {
                        $('#profile').html(data);
                        if (profile) {
                            $('#profile').val(profile);
                            $('#diameter').prop('disabled', false);
                            $.ajax({
                                url: "{{ route('tyres.getDiameters') }}",
                                type: "GET",
                                data: { width: width, profile: profile, vehicleType: vehicleTypeFromUrl },
                                success: function (data) {
                                    $('#diameter').html(data);
                                    if (diameter) {
                                        $('#diameter').val(diameter);
                                    }
                                }
                            });
                        }
                    },
                });
            }

            updateBreadcrumb();
            loadFilterOptions();
        });
    </script>
    <script>
        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
        $(document).on('click', '#add-to-cart', function (e) {
            e.preventDefault();
            const tyreId = $(this).data('id');
            const qty = $(this).closest('.product-wrap').find('.cart-plus-minus-box').val();
            const fittingType = getURLParameter('fitting_type') || 'fully_fitted';
            const type = 'tyre';
            $.ajax({
                url: "{{ route('cart.add') }}",
                type: "POST",
                data: {
                    id: tyreId,
                    type: type,
                    fitting_type: fittingType,
                    qty: qty,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        // Original success logic (item added successfully)
                        Swal.fire({
                            title: 'Product added to cart!',
                            text: 'Do you want to add more items or Continue?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Continue',
                            cancelButtonText: 'Add More',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('grages') }}";
                            } else {
                                location.reload();
                            }
                        });
                        let newTotalQuantity = response.totalQuantity;
                        $('.count-style').text(newTotalQuantity);
                        let newTotalPrice = response.cartTotalPrice;
                        $('.shop-total').text('£' + newTotalPrice);
                        let newCartItems = '';
                        let newSubTotal = 0;
                        let newVatTotal = 0;
                        let newGrandTotal = 0;
                        for (const key in response.product) {
                            if (response.product.hasOwnProperty(key)) {
                                const item = response.product[key];
                                const itemTotalPrice = (item.price * item.quantity).toFixed(2);
                                const itemVAT = item.tax_class_id == 9 ? itemTotalPrice * 0.20 : 0;
                                newSubTotal += parseFloat(itemTotalPrice);
                                newVatTotal += parseFloat(itemVAT);
                                newGrandTotal += parseFloat(itemTotalPrice) + parseFloat(itemVAT);
                                newCartItems += `
                                                    <li class="single-shopping-cart" id="cart-item-${item.id}">
                                                        <div class="shopping-cart-title">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <h4>${item.model}</h4>
                                                                <h6>£${itemTotalPrice}</h6>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="item_width">
                                                                    ${item.desc ? `<h6>${item.desc}</h6>` : ''}
                                                                </div>
                                                                <h4 class="quantity">Qty: ${item.quantity}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="shopping-cart-delete">
                                                            <a class="delete-item" href="javascript:void(0);" data-id="${item.id}">
                                                                <i class="fa fa-times-circle"></i>
                                                            </a>
                                                        </div>
                                                    </li>
                                                `;
                            }
                        }
                        $('#cart-items-list').html(newCartItems);
                        $('#sub-total').text('£' + parseFloat(response.cartSubTotal).toFixed(2));
                        $('#vat-total').text('£' + parseFloat(response.vatTotal).toFixed(2));
                        if (parseFloat(response.shippingPricePerJob) > 0 || parseFloat(response.shippingPricePerTyre) > 0) {
                            const calloutCharges = parseFloat(response.shippingPricePerJob) + parseFloat(response.shippingPricePerTyre);
                            const shippingVAT = parseFloat(response.shippingVAT);
                            $('.callout-charges').remove();
                            const calloutChargesHTML = `
                                                <h4 class="callout-charges">
                                                    Callout Charges: £${calloutCharges.toFixed(2)}
                                                </h4>
                                            `;
                            $('.shopping-cart-total').append(calloutChargesHTML);
                            const vatTotalWithShipping = parseFloat(response.vatTotal);
                            $('#vat-total').text('£' + vatTotalWithShipping.toFixed(2));
                            const grandTotal = parseFloat(response.cartSubTotal) + vatTotalWithShipping + calloutCharges;
                            $('#grand-total').text('£' + grandTotal.toFixed(2));
                        } else {
                            $('#grand-total').text('£' + parseFloat(response.cartTotalPrice).toFixed(2));
                        }
                        if (newTotalQuantity > 0) {
                            $('.shopping-cart-content .text-center').hide();
                            $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').show();
                        } else {
                            $('.shopping-cart-content .text-center').show();
                            $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').hide();
                        }
                    } else if (response.needs_confirmation) {
                        // Handle fitting type conflict
                        const requestedItem = response.requested_item;
                        const existingFittingType = response.existing_fitting_type;

                        Swal.fire({
                            title: 'Fitting Type Mismatch',
                            html: response.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Clear Cart & Add Item',
                            cancelButtonText: 'Cancel',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "{{ route('cart.clear') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function (clearResponse) {
                                        if (clearResponse.success) {
                                            $.ajax({
                                                url: "{{ route('cart.add') }}",
                                                type: "POST",
                                                data: {
                                                    id: requestedItem.id,
                                                    type: requestedItem.type,
                                                    fitting_type: requestedItem.fitting_type,
                                                    qty: requestedItem.qty,
                                                    _token: "{{ csrf_token() }}"
                                                },
                                                success: function (newResponse) {
                                                    if (newResponse.success) {
                                                        Swal.fire({
                                                            title: 'Product added to cart!',
                                                            text: 'Do you want to add more items or Continue?',
                                                            icon: 'success',
                                                            showCancelButton: true,
                                                            confirmButtonText: 'Continue',
                                                            cancelButtonText: 'Add More',
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                window.location.href = "{{ route('grages') }}";
                                                            } else {
                                                                location.reload();
                                                            }
                                                        });
                                                        let newTotalQuantity = newResponse.totalQuantity;
                                                        $('.count-style').text(newTotalQuantity);
                                                        let newTotalPrice = newResponse.cartTotalPrice;
                                                        $('.shop-total').text('£' + newTotalPrice);
                                                        let newCartItems = '';
                                                        let newSubTotal = 0;
                                                        let newVatTotal = 0;
                                                        let newGrandTotal = 0;
                                                        for (const key in newResponse.product) {
                                                            if (newResponse.product.hasOwnProperty(key)) {
                                                                const item = newResponse.product[key];
                                                                const itemTotalPrice = (item.price * item.quantity).toFixed(2);
                                                                const itemVAT = item.tax_class_id == 9 ? itemTotalPrice * 0.20 : 0;
                                                                newSubTotal += parseFloat(itemTotalPrice);
                                                                newVatTotal += parseFloat(itemVAT);
                                                                newGrandTotal += parseFloat(itemTotalPrice) + parseFloat(itemVAT);
                                                                newCartItems += `
                                                                                    <li class="single-shopping-cart" id="cart-item-${item.id}">
                                                                                        <div class="shopping-cart-title">
                                                                                            <div class="d-flex justify-content-between mb-2">
                                                                                                <h4>${item.model}</h4>
                                                                                                <h6>£${itemTotalPrice}</h6>
                                                                                            </div>
                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                <div class="item_width">
                                                                                                    ${item.desc ? `<h6>${item.desc}</h6>` : ''}
                                                                                                </div>
                                                                                                <h4 class="quantity">Qty: ${item.quantity}</h4>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="shopping-cart-delete">
                                                                                            <a class="delete-item" href="javascript:void(0);" data-id="${item.id}">
                                                                                                <i class="fa fa-times-circle"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </li>
                                                                                `;
                                                            }
                                                        }
                                                        $('#cart-items-list').html(newCartItems);
                                                        $('#sub-total').text('£' + parseFloat(newResponse.cartSubTotal).toFixed(2));
                                                        $('#vat-total').text('£' + parseFloat(newResponse.vatTotal).toFixed(2));
                                                        if (parseFloat(newResponse.shippingPricePerJob) > 0 || parseFloat(newResponse.shippingPricePerTyre) > 0) {
                                                            const calloutCharges = parseFloat(newResponse.shippingPricePerJob) + parseFloat(newResponse.shippingPricePerTyre);
                                                            const shippingVAT = parseFloat(newResponse.shippingVAT);
                                                            $('.callout-charges').remove();
                                                            const calloutChargesHTML = `
                                                                                <h4 class="callout-charges">
                                                                                    Callout Charges: £${calloutCharges.toFixed(2)}
                                                                                </h4>
                                                                            `;
                                                            $('.shopping-cart-total').append(calloutChargesHTML);
                                                            const vatTotalWithShipping = parseFloat(newResponse.vatTotal);
                                                            $('#vat-total').text('£' + vatTotalWithShipping.toFixed(2));
                                                            const grandTotal = parseFloat(newResponse.cartSubTotal) + vatTotalWithShipping + calloutCharges;
                                                            $('#grand-total').text('£' + grandTotal.toFixed(2));
                                                        } else {
                                                            $('#grand-total').text('£' + parseFloat(newResponse.cartTotalPrice).toFixed(2));
                                                        }
                                                        if (newTotalQuantity > 0) {
                                                            $('.shopping-cart-content .text-center').hide();
                                                            $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').show();
                                                        } else {
                                                            $('.shopping-cart-content .text-center').show();
                                                            $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').hide();
                                                        }
                                                    } else {
                                                        // Failed to add item after clearing (shouldn't happen often)
                                                        console.error("Failed to add item after clearing cart:", newResponse);
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error',
                                                            text: newResponse.message || 'Failed to add the item after clearing the cart.',
                                                        });
                                                    }
                                                },
                                                error: function (xhr, status, error) {
                                                    console.error("Error adding item after clearing cart:", error);
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'An unexpected error occurred',
                                                        text: 'Please try again later.',
                                                    });
                                                }
                                            });
                                        } else {
                                            // Failed to clear cart
                                            console.error("Failed to clear cart:", clearResponse);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: clearResponse.message || 'Failed to clear the cart.',
                                            });
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error("Error clearing cart:", error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'An unexpected error occurred',
                                            text: 'Please try again later.',
                                        });
                                    }
                                });
                            }
                        });

                    } else {
                        try {
                            if (!response.success) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: response.message,
                                    showCancelButton: true,
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'An unexpected error occurred',
                                text: 'Please try again later.',
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    // Handle network/HTTP errors
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (!response.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Insufficient Stock',
                                text: response.message || 'An unexpected error occurred.',
                                showCancelButton: true,
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'An unexpected error occurred',
                            text: 'Please try again later.',
                        });
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const postcodeData = {!! json_encode(session('postcode_data')) !!};
            const fittingType = getURLParameter('fitting_type') || 'fully_fitted';

            const postcodeForm = document.getElementById('postcodeForm');
            if (postcodeForm) {
                postcodeForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const postcode = document.getElementById('postcode').value;
                    const errorMsg = document.getElementById('postcodeErrorMsg');
                    const resultDiv = document.getElementById('resultMessage');
                    const continueButton = document.getElementById('continueButton');
                    const changePostcodeButton = document.getElementById('changePostcodeButton');

                    errorMsg.textContent = '';
                    resultDiv.innerHTML = '';

                    fetch('/calculate-shipping', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ postcode: postcode })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'An unexpected error occurred.');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                errorMsg.textContent = data.error;
                            } else {
                                window.postcodeResponseData = data;
                                resultDiv.innerHTML = `
                                        Postcode: ${data.postcode}<br>
                                        Distance: ${data.distance_in_miles} Miles<br>
                                        Total Price: £${data.total_price}
                                    `;
                                continueButton.style.display = 'inline-block';
                                changePostcodeButton.style.display = 'inline-block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            errorMsg.textContent = error.message || 'An unexpected error occurred. Please try again.';
                        });
                });
            }

            const continueButton = document.getElementById('continueButton');
            if (continueButton) {
                continueButton.addEventListener('click', function () {
                    const data = window.postcodeResponseData;
                    if (!data) {
                        console.error("No postcode data available to store.");
                        alert("No postcode data available. Please submit the postcode form first.");
                        return;
                    }
                    fetch('/store-postcode-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'Failed to save postcode data.');
                                });
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                location.reload();
                            } else {
                                alert('Failed to save postcode data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(error.message || 'An unexpected error occurred while saving postcode data.');
                        });
                });
            }

            // Mobile Change Postcode Button Listener
            const changePostcodeButton = document.getElementById('changePostcodeButton');
            if (changePostcodeButton) {
                changePostcodeButton.addEventListener('click', function () {
                    document.getElementById('postcode').value = '';
                    document.getElementById('resultMessage').innerHTML = '';
                    document.getElementById('continueButton').style.display = 'none';
                    document.getElementById('changePostcodeButton').style.display = 'none';
                });
            }

            const mailorderPostcodeForm = document.getElementById('mailorderpostcodeForm');
            if (mailorderPostcodeForm) {
                mailorderPostcodeForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const postcode = document.getElementById('postcode').value;
                    const errorMsg = document.getElementById('postcodeErrorMsg');
                    const resultDiv = document.getElementById('resultMailorderMessage');
                    const continueMailorderButton = document.getElementById('continueMailorderButton');
                    const changeMailorderPostcodeButton = document.getElementById('changeMailorderPostcodeButton');

                    errorMsg.textContent = '';
                    resultDiv.innerHTML = '';

                    fetch('/calculate-mailshipping', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ postcode: postcode })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'An unexpected error occurred.');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                errorMsg.textContent = data.error;
                            } else {
                                window.postcodeResponseData = data;
                                resultDiv.innerHTML = `
                                        Postcode: ${data.postcode}<br>
                                        Distance: ${data.distance_in_miles} Miles<br>
                                        Total Price: £${data.total_price}
                                    `;
                                continueMailorderButton.style.display = 'inline-block';
                                changeMailorderPostcodeButton.style.display = 'inline-block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            errorMsg.textContent = error.message || 'An unexpected error occurred. Please try again.';
                        });
                });
            }

            const continueMailorderButton = document.getElementById('continueMailorderButton');
            if (continueMailorderButton) {
                continueMailorderButton.addEventListener('click', function () {
                    const data = window.postcodeResponseData;
                    if (!data) {
                        console.error("No postcode data available to store.");
                        alert("No postcode data available. Please submit the postcode form first.");
                        return;
                    }
                    fetch('/store-mailpostcode-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'Failed to save postcode data.');
                                });
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                location.reload();
                            } else {
                                alert('Failed to save postcode data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(error.message || 'An unexpected error occurred while saving postcode data.');
                        });
                });
            }

            const changeMailorderPostcodeButton = document.getElementById('changeMailorderPostcodeButton');
            if (changeMailorderPostcodeButton) {
                changeMailorderPostcodeButton.addEventListener('click', function () {
                    document.getElementById('postcode').value = '';
                    document.getElementById('resultMailorderMessage').innerHTML = '';
                    document.getElementById('continueMailorderButton').style.display = 'none';
                    document.getElementById('changeMailorderPostcodeButton').style.display = 'none';
                });
            }

            if (fittingType === 'mobile_fitted' && !postcodeData) {
                const postcodeModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
                    backdrop: 'static',
                    keyboard: false,
                });
                postcodeModal.show();
            }

            if (fittingType === 'mailorder' && !postcodeData) {
                const mailorderpostcodeModal = new bootstrap.Modal(document.getElementById('mailorderpostcodeModal'), {
                    backdrop: 'static',
                    keyboard: false,
                });
                mailorderpostcodeModal.show();
            }

        });
    </script>
    <script>
        let currentPage = 1;
        let isLoading = false;
        $(document).on('click', '#loadMoreBtn', function () {
            if (isLoading) return;
            isLoading = true;
            currentPage++;
            const urlParams = new URLSearchParams(window.location.search);
            const width = urlParams.get('width');
            const profile = urlParams.get('profile');
            const diameter = urlParams.get('diameter');
            const fuel = urlParams.getAll('fuel[]');
            const season = urlParams.getAll('season[]');
            const wetGrip = urlParams.getAll('wetGrip[]');
            const tyreBrand = urlParams.getAll('tyreBrand[]');
            const speedRating = urlParams.getAll('speedRating[]');
            const loadIndex = urlParams.getAll('loadIndex[]');
            const noiseLevel = urlParams.getAll('noiseLevel[]');
            const runflat = urlParams.get('runflat');
            const extraload = urlParams.get('extraload');
            const vehicleType = urlParams.get('vehicle_type');
            const orderType = urlParams.get('ordertype');
            $(this).text('Loading...');
            $.ajax({
                url: "{{ route('tyres.filter') }}",
                type: "GET",
                data: {
                    width: width,
                    profile: profile,
                    diameter: diameter,
                    fuel: fuel,
                    season: season,
                    wetGrip: wetGrip,
                    tyreBrand: tyreBrand,
                    speedRating: speedRating,
                    loadIndex: loadIndex,
                    noiseLevel: noiseLevel,
                    runflat: runflat,
                    extraload: extraload,
                    vehicle_type: vehicleType,
                    ordertype: orderType,
                    noiseLevel: noiseLevel,
                    page: currentPage
                },
                success: function (response) {
                    if (response.tyres) {
                        $('#tyreList').append(response.tyres);
                    }
                    isLoading = false;
                    $('#loadMoreBtn').text('Load More');
                    if (!response.has_more_pages) {
                        $('#loadMoreBtn').hide();
                    }
                },
                error: function () {
                    $('#loadMoreBtn').text('Failed to load more tyres.');
                    isLoading = false;
                }
            });
        });
       function resetAllFiltersExceptVehicleType(newVehicleType) {
    $('#width').empty().append('<option value="">Select</option>');
    $('#profile').empty().prop('disabled', true).append('<option value="">Select Profile</option>');
    $('#diameter').empty().prop('disabled', true).append('<option value="">Select Diameter</option>');
    $('#loadIndexSelect').empty().append('<option value="">Any Load Index</option>');
    $('#speedIndexSelect').empty().append('<option value="">Any Speed Index</option>');
    $('#load_index').val('');
    $('#speed_index').val('');
    selectedLoadIndex = '';
    selectedSpeedIndex = '';
    $('#orderTypeSelect').empty().append('<option value="">Any Order Type</option>');
    $('#ordertype').val(''); 
    selectedOrderType = '';
    $('#runflatCheckbox').prop('checked', false);
    $('#extraloadCheckbox').prop('checked', false);
    $('input[name="fuel[]"]').prop('checked', false);
    $('input[name="season[]"]').prop('checked', false);
    $('input[name="wetGrip[]"]').prop('checked', false);
    $('input[name="tyreBrand[]"]').prop('checked', false);
    $('input[name="noiseLevel[]"]').prop('checked', false);

    const urlParams = new URLSearchParams(window.location.search);
    for (let key of urlParams.keys()) {
        if (key !== 'vehicle_type') urlParams.delete(key);
    }
    if (newVehicleType) {
        urlParams.set('vehicle_type', newVehicleType);
    } else {
        urlParams.delete('vehicle_type');
    }
    const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    window.history.replaceState({ path: newUrl }, '', newUrl);
    updateBreadcrumb();
    $.ajax({
        url: "{{ route('tyres.getWidths') }}",
        type: "GET",
        data: { vehicleType: newVehicleType },
        success: function (htmlOptions) {
            $('#width').html(htmlOptions);
        },
        error: function () {
            console.error("Failed to load widths for vehicle type:", newVehicleType);
        }
    });

    loadOrderTypesByVehicleType(newVehicleType);

}
    </script>
@endsection