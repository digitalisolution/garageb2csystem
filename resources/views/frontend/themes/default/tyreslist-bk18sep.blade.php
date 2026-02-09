@extends('layouts.app')

@section('content')
    <div class="breadcrumb-area pt-20 pb-20 bg-gray-3">
        <div class="container">
            <div class="d-flex align-items-center flex-wrap justify-content-center">
                <div class="breadcrumb-content text-center">
                    <h1 id="breadcrumbText">Your Result</h1>
                </div>
                <div class="shop-top-bar ml-auto">
                    <div class="select-shoing-wrap">
                    </div>
                    <div class="filter-active">
                        <a href="javascript:void(0);" class="btn btn-theme text-white"><i class="fa fa-plus"></i> Filter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="shop-area">
        <div class="container">
            <div class="product-filter-wrapper mt-3">
                <div class="row">
                    <form id="filterForm" class="mb-4">
                        <div class="product-filter">
                            <h5>Change Your Size</h5>
                        </div>
                        <div class="row">
                            <!-- Width -->
                            <div class="col-md-3 col-sm-6 col-4">
                                <select name="width" id="width" class="form-control">
                                    <option value="">Select Width</option>
                                    @foreach ($widths as $width)
                                        <option value="{{ $width }}">{{ $width }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Profile -->
                            <div class="col-md-3 col-sm-6 col-4">
                                <select name="profile" id="profile" class="form-control" disabled>
                                    <option value="">Select Profile</option>
                                </select>
                            </div>

                            <!-- Diameter -->
                            <div class="col-md-3 col-sm-6 col-4">
                                <select name="diameter" id="diameter" class="form-control" disabled>
                                    <option value="">Select Diameter</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <!-- Product Filter -->
                    <div class="col-md-3 col-sm-6 col-12 mb-30">
                        <div class="product-filter brand_filter">
                            <h5>Tyres Brand</h5>
                            <ul class="color-filter" id="tyreBrand">
                            </ul>
                        </div>
                    </div>
                    <!-- Product Filter -->
                    <div class="col-md-3 col-sm-6 col-6 mb-30">
                        <div class="product-filter">
                            <h5>Season</h5>
                            <ul class="color-filter" id="getSeason">

                            </ul>
                        </div>
                        <div class="product-filter mt-30">
                            <h5>Application</h5>
                            <ul class="color-filter">
                                <li>
                                    <label>
                                        <input type="checkbox" name="runflat" value="1">
                                        <a class="filter-label">Run Flat (RFT)</a>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="extraload" value="1">
                                        <a class="filter-label">Reinforced</a>
                                    </label>
                                </li>
                            </ul>

                        </div>
                    </div>
                    <!-- Product Filter -->
                    <div class="col-md-3 col-sm-6 col-6 mb-30">
                        <div class="product-filter">
                            <h5>Fuel Efficiency</h5>
                            <ul class="color-filter" id="fuelFilter">
                            </ul>
                        </div>
                    </div>

                    <!-- Product Filter -->
                    <div class="col-md-3 col-sm-6 col-12 mb-30">
                        <div class="product-filter">
                            <h5>Wet Grip</h5>
                            <ul class="color-filter" id="wetGrip">
                            </ul>
                        </div>
                        
                        <!-- New Filters -->
                        <div class="product-filter mt-30">
                            <h5>Speed Rating</h5>
                            <ul class="color-filter" id="speedRatingFilter">
                            </ul>
                        </div>
                        
                        <div class="product-filter mt-30">
                            <h5>Load Index</h5>
                            <ul class="color-filter" id="loadIndexFilter">
                            </ul>
                        </div>
                        
                        <div class="product-filter mt-30">
                            <h5>Noise Level</h5>
                            <ul class="color-filter" id="noiseLevelFilter">
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <button type="button" id="searchButton" class="btn btn-filter btn-block" disabled>Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($recommendedTyres))
        <div class="shop-area pt-40 pb-40 bg-dark">
            <div class="container">
                <h3 class="text-white mb-20 text-center">Recommended Tyres</h3>
                <div id="recommendedTyreList">
                    @include('recommended-tyres', ['recommendedTyres' => $recommendedTyres, 'message' => $message])
                </div>
            </div>
        </div>
    @endif

    <div class="shop-area pt-95 pb-100">
        <div class="container">
            <div class="row flex-row-reverse">
                <div class="col-lg-12">
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
                    <!-- Postcode Form -->
                    <form id="postcodeForm" action="/submit-postcode" method="POST">
                        <div class="col-lg-8 m-auto text-center">
                            <input type="text" class="form-control mb-2" name="postcode" id="postcode" maxlength="8"
                                placeholder="ENTER POSTCODE" required>
                            <button type="submit" class="btn btn-theme btn-block">Submit</button>
                        </div>
                    </form>
                    <!-- Error Message -->
                    <div class="mt-2">
                        <span id="postcodeErrorMsg" class="text-danger"></span>
                    </div>
                    <!-- Result Message -->
                    <div id="resultMessage" class="mt-3"></div>
                    <!-- Action Buttons -->
                    <div class="mt-3" id="actionButtons">
                        <button id="continueButton" class="btn btn-success me-2" style="display: none;">Continue</button>
                        <button id="changePostcodeButton" class="btn btn-secondary" style="display: none;">Change
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
        // Load dynamic filter options
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
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Season Options
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
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Wet Grip Options
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
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Tyre Brand Options
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
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Speed Rating Options
            $.ajax({
                url: "{{ route('tyres.getSpeedRatingOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#speedRatingFilter').empty();
                    $.each(data, function (index, speedRating) {
                        $('#speedRatingFilter').append(`
                            <li>
                                <label>
                                    <input type="checkbox" name="speedRating[]" value="${speedRating}">
                                    <a>${speedRating}</a>
                                </label>
                            </li>
                        `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Load Index Options
            $.ajax({
                url: "{{ route('tyres.getLoadIndexOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#loadIndexFilter').empty();
                    $.each(data, function (index, loadIndex) {
                        $('#loadIndexFilter').append(`
                            <li>
                                <label>
                                    <input type="checkbox" name="loadIndex[]" value="${loadIndex}">
                                    <a>${loadIndex}</a>
                                </label>
                            </li>
                        `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });

            // Noise Level Options
            $.ajax({
                url: "{{ route('tyres.getNoiseLevelOptions') }}",
                type: "GET",
                success: function (data) {
                    $('#noiseLevelFilter').empty();
                    $.each(data, function (index, noiseLevel) {
                        $('#noiseLevelFilter').append(`
                            <li>
                                <label>
                                    <input type="checkbox" name="noiseLevel[]" value="${noiseLevel}">
                                    <a>${noiseLevel} dB</a>
                                </label>
                            </li>
                        `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        loadFilterOptions();

        // Handle Width Change
        $('#width').change(function () {
            const width = $(this).val();
            updateURLParams('width', width);

            $('#profile').prop('disabled', true).html('<option value="">Select Profile</option>');
            $('#diameter').prop('disabled', true).html('<option value="">Select Diameter</option>');
            $('#searchButton').prop('disabled', true);

            if (width) {
                $.ajax({
                    url: "{{ route('tyres.getProfiles') }}",
                    type: "GET",
                    data: { width: width },
                    success: function (data) {
                        $('#profile').prop('disabled', false).html(data);
                    },
                });
            }
        });

        // Handle Profile Change
        $('#profile').change(function () {
            const profile = $(this).val();
            updateURLParams('profile', profile);

            $('#diameter').prop('disabled', true).html('<option value="">Select Diameter</option>');
            $('#searchButton').prop('disabled', true);

            if (profile) {
                const width = $('#width').val();
                $.ajax({
                    url: "{{ route('tyres.getDiameters') }}",
                    type: "GET",
                    data: { width: width, profile: profile },
                    success: function (data) {
                        $('#diameter').prop('disabled', false).html(data);
                    },
                });
            }
        });

        // Handle Diameter Change
        $('#diameter').change(function () {
            const diameter = $(this).val();
            updateURLParams('diameter', diameter);

            if (diameter) {
                $('#searchButton').prop('disabled', false);
            } else {
                $('#searchButton').prop('disabled', true);
            }
        });

        // Handle Search Button Click
        $('#searchButton').click(function () {
            const width = $('#width').val();
            const profile = $('#profile').val();
            const diameter = $('#diameter').val();
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
            const speedRating = $('input[name="speedRating[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const loadIndex = $('input[name="loadIndex[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const noiseLevel = $('input[name="noiseLevel[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            const runflat = $('input[name="runflat"]:checked').val();
            const extraload = $('input[name="extraload"]:checked').val();

            // Convert to 1 or 0
            const runflatValue = runflat ? 1 : 0;
            const extraloadValue = extraload ? 1 : 0;

            // Perform AJAX Request to get filtered tyres
            $.ajax({
                url: "{{ route('tyres.filter') }}",
                type: "GET",
                data: {
                    width: width,
                    profile: profile,
                    diameter: diameter,
                    fuel: fuelEfficiency,
                    season: getSeason,
                    wetGrip: getWetGrip,
                    tyreBrand: getTyreBrand,
                    speedRating: speedRating,
                    loadIndex: loadIndex,
                    noiseLevel: noiseLevel,
                    runflat: runflat,
                    extraload: extraload
                },
                success: function (response) {
                    $('#tyreList').html(response.tyres);
                    if (response.recommendedTyres.trim() !== '') {
                        $('#recommendedTyreList').html(response.recommendedTyres).closest('.shop-area').show();
                    } else {
                        $('#recommendedTyreList').closest('.shop-area').hide();
                    }
                    $('#paginationContainer').html(response.pagination);
                    $('.product-filter-wrapper').fadeOut('slow');
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });

        function updateURLParams(param, value) {
            const urlParams = new URLSearchParams(window.location.search);
            if (value) {
                urlParams.set(param, value);
            } else {
                urlParams.delete(param);
            }
            history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);

            // Update breadcrumb after URL update
            updateBreadcrumb();
        }

        // Update the breadcrumb dynamically
        function updateBreadcrumb() {
            const urlParams = new URLSearchParams(window.location.search);
            const width = urlParams.get('width');
            const profile = urlParams.get('profile');
            const diameter = urlParams.get('diameter');
            let breadcrumbText = 'Your Result ';

            if (width) {
                breadcrumbText += `${width} `;
            }
            if (profile) {
                breadcrumbText += `${profile} `;
            }
            if (diameter) {
                breadcrumbText += `R${diameter}`;
            }

            document.getElementById('breadcrumbText').textContent = breadcrumbText;
        }

        // Pre-fill the filter form and update the breadcrumb
        $(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const width = urlParams.get('width');
            const profile = urlParams.get('profile');
            const diameter = urlParams.get('diameter');
            const fittingType = urlParams.get('fitting_type');

            if (width) {
                $('#width').val(width);
                $('#profile').prop('disabled', false);
                $.ajax({
                    url: "{{ route('tyres.getProfiles') }}",
                    type: "GET",
                    data: { width: width },
                    success: function (data) {
                        $('#profile').html(data);
                        if (profile) {
                            $('#profile').val(profile);
                            $('#diameter').prop('disabled', false);
                            $.ajax({
                                url: "{{ route('tyres.getDiameters') }}",
                                type: "GET",
                                data: { width: width, profile: profile },
                                success: function (data) {
                                    $('#diameter').html(data);
                                    if (diameter) {
                                        $('#diameter').val(diameter);
                                        $('#searchButton').prop('disabled', false);
                                    }
                                }
                            });
                        }
                    },
                });
            }
            // Initialize breadcrumb
            updateBreadcrumb();
        });

    </script>

    <script>
        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Handle "Add to Cart" button clicks for tyres
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
                    } else {
                        try {
                            if (!response.success) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Insufficient Stock',
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
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (!response.success) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Insufficient Stock',
                                text: response.message,
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
            const user_postcode = {!! json_encode(session('user_postcode')) !!};
            const fittingType = getURLParameter('fitting_type') || 'fully_fitted';
            
            if (fittingType === 'mobile_fitted' && (!postcodeData || !user_postcode)) {
                const postcodeModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
                    backdrop: 'static',
                    keyboard: false,
                });
                postcodeModal.show();
                
                document.getElementById('postcodeForm').addEventListener('submit', function (event) {
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

                document.getElementById('changePostcodeButton').addEventListener('click', function () {
                    document.getElementById('postcode').value = '';
                    document.getElementById('resultMessage').innerHTML = '';
                    document.getElementById('continueButton').style.display = 'none';
                    document.getElementById('changePostcodeButton').style.display = 'none';
                });

                document.getElementById('continueButton').addEventListener('click', function () {
                    const data = window.postcodeResponseData;

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
    </script>
@endsection