@extends('layouts.app')

<?php
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
?>

@section('content')
    <div class="breadcrumb-area pt-35 pb-35 bg-dark">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li class="active">Book Your Service</li>
                </ul>
            </div>
        </div>
    </div>

   <div class="pt-60 pb-60">
    <div class="container">
        <div class="garage_listing_filter">
            <div class="row">
                <div class="col-lg-5">
                    <h4>Showing local fitters in the area of</h4>
                    <div class="reg_filter p-0 border-0 d-flex gap-2">
                        <input 
                            id="postcode" 
                            type="text" 
                            class="form-control postcode uppercase" 
                            value="{{ $user_postcode ?? '' }}" 
                            placeholder="Enter Postcode" 
                            name="postcode">
                        
                        <select class="form-select" id="distance-input">
                            <option value="5">5 Miles</option>
                            <option value="10">10 Miles</option>
                            <option value="15" {{ (session('distance') == 15 || !session('distance')) ? 'selected' : '' }}>15 Miles</option>
                            <option value="25">25 Miles</option>
                            <option value="50">50 Miles</option>
                            <option value="100">100 Miles</option>
                        </select>
                        
                        <button type="button" class="btn btn-sm btn-theme fw-bold px-3" id="filter-btn">Go</button>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="filter_type">
                        <h4>Fitter type</h4>
                        <div class="d-flex gap-4 align-items-center fw-bold flex-wrap">
                            <div class="product-filter d-none">
                                <label class="d-flex gap-2">
                                    <input type="checkbox" id="garage-fitter" value="Garage Fitter"> Garage Fitter
                                </label>
                            </div>
                            <div class="product-filter d-none">
                                <label class="d-flex gap-2">
                                    <input type="checkbox" id="mobile-fitter" value="Mobile Fitter"> Mobile Fitter
                                </label>
                            </div>
                            <div class="product-filter sort">
                                <select name="sort" id="sort-input" class="form-control fw-bold">
                                    <option value="">Sort</option>
                                    <option value="distance" {{ session('sort') == 'distance' ? 'selected' : '' }}>Nearest First</option>
                                    <option value="rating" {{ session('sort') == 'rating' ? 'selected' : '' }}>Best Rating</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h2 class="mb-4 fw-bold">Our top choices for you</h2>
            <div id="garage-listing-results">
                @include('garages.partials.list', ['garages' => $garages, 'domain' => $domain])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filter-btn');
    const postcodeInput = document.getElementById('postcode');
    const distanceInput = document.getElementById('distance-input');
    const sortInput = document.getElementById('sort-input');
    const garageFitter = document.getElementById('garage-fitter');
    const mobileFitter = document.getElementById('mobile-fitter');

    // Set initial values from session or defaults
    const initialPostcode = "{{ $user_postcode ?? '' }}";
    if (initialPostcode) {
        postcodeInput.value = initialPostcode;
    }

    filterBtn.addEventListener('click', applyFilter);

    // Also trigger on Enter key
    postcodeInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') applyFilter();
    });

    // Reapply on sort/fitter change
    [sortInput, garageFitter, mobileFitter].forEach(el => {
        el.addEventListener('change', applyFilter);
    });

    // Apply filter on page load if postcode exists
    if (initialPostcode) {
        setTimeout(applyFilter, 500);
    }

    async function applyFilter() {
        const postcode = postcodeInput.value.trim();
        const distance = distanceInput.value;
        const sort = sortInput.value;
        const fitterTypes = [];
        if (garageFitter.checked) fitterTypes.push(garageFitter.value);
        if (mobileFitter.checked) fitterTypes.push(mobileFitter.value);

        if (!postcode) {
            alert('Please enter a postcode.');
            return;
        }

        // Show loading
        document.getElementById('garage-listing-results').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        try {
            const response = await fetch("{{ route('garage.filter') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    postcode, 
                    distance, 
                    sort, 
                    fitter_types: fitterTypes 
                })
            });

            if (response.ok) {
            // Success - return HTML content
            html = await response.text();
        } else if (response.status === 422) {
            // Validation error - return JSON error message
            const errorData = await response.json();
            html = `<div class="alert alert-danger">${errorData.error || 'Invalid postcode or filters.'}</div>`;
        } else {
            // Other errors
            html = `<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>`;
        }
            document.getElementById('garage-listing-results').innerHTML = html;

            // Save postcode to session via hidden route
            fetch("{{ route('garage.save-postcode') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ postcode })
            });

        } catch (err) {
            console.error('Error:', err);
            document.getElementById('garage-listing-results').innerHTML = '<div class="text-danger text-center">Error loading garages. Please try again.</div>';
        }
    }
});
</script>
@endpush