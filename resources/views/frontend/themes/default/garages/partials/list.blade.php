@if(isset($garages) && !$garages->isEmpty())
    <div class="garage_listing">
        @foreach ($garages as $garage)
            <div class="listing_card" data-type="{{ $garage->fitter_type ?? 'Garage Fitter' }}">
                <div class="top_area">
                    <h4>{{ $garage->garage_name ?? 'Approved Partner' }}</h4>
                    <div class="stars">
                        <a href="{{ route('garage.profile', $garage->id) }}" target="_blank">
                        @php 
                            $rating = $garage->reviews->avg('rating') ?? 0; 
                            $rating = round($rating, 1);
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= $rating ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                            <span>({{ $rating }} Ratings)</span>
                        </a>
                    </div>
                </div>

                <div class="midd_area">
                    <div class="description_card">
                        @php
                            $logoPath = "frontend/{$domain}/img/garage_logo/{$garage->garage_logo}";
                            $themeLogo = "frontend/themes/{$garage->theme}/img/garage_logo/{$garage->garage_logo}";
                            $defaultLogo = "frontend/themes/default/img/logo/logo.png";
                            $src = file_exists(public_path($logoPath)) ? $logoPath :
                                   (file_exists(public_path($themeLogo)) ? $themeLogo : $defaultLogo);
                        @endphp
                        <a href="{{ route('garage.profile', $garage->id) }}">
                            <img src="{{ asset($src) }}?v={{ time() }}" alt="Logo" width="auto" loading="lazy">
                        </a>
                        <div class="mt-3 mb-2">
                            <h5 class="fw-bold mb-1 text-capitalize">{{ $garage->garage_name }}</h5>
                            <p class="mb-1">{{ $garage->garage_street }}, {{ $garage->garage_city }}, {{ $garage->garage_zone }}</p>
                            @if(isset($garage->garage_postcode))
                                <p class="mb-1">Postcode: {{ $garage->garage_postcode }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="description_card">
                        <h5 class="fw-bold mb-2">
                            @if(isset($garage->distance))
                                {{ number_format($garage->distance, 2) }} miles away
                            @else
                                Distance not available
                            @endif
                            @if($garage->garage_google_map_link)
                                <a href="{{ $garage->garage_google_map_link }}" target="_blank" class="ms-3 small text-link">View map</a>
                            @endif
                        </h5>
                        <div class="border py-1 px-2 rounded-3 bg-light mb-1">
                            <p class="fw-bold small">{{ $garage->garage_garage_opening_time }}</p>
                        </div>
                       <a href="{{ route('book.now', $garage->id) }}" class="btn btn-theme w-100 fw-bold mt-2">Book Now</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted">No garages found within the selected distance.</div>
@endif