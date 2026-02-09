@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="container py-5">
    <div class="row">
        <!-- Left Side: Garage Info -->
        <div class="col-lg-8">
                <!-- @if($garages->garage_banner)
                    <img src="{{ asset('storage/' . $garages->garage_banner) }}" alt="Banner" class="img-fluid rounded mb-3" style="height: 200px; object-fit: cover; width: 100%;">
                @endif -->
                <img src="frontend/www-garage-automation-co-uk/img/banner1.webp" alt="Banner" class="img-bank">
                <div class="garage_bank">
                    <!-- @if($garages->garage_logo)
                        <img src="{{ asset('storage/' . $garages->garage_logo) }}" alt="Logo" class="rounded" width="80" height="80">
                    @endif -->
                    <img src="frontend/www-garage-automation-co-uk/img/ng-logo.webp" alt="Banner" class="img-adjust" width="auto" height="100">
                    <div>
                        <h3 class="fw-bold">{{ $garages->garage_name }}</h3>
                        <div class="text-warning">
                            ⭐ {{ number_format($avgRating, 1) }} ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})
                        </div>
                    </div>
                </div>
                <div class="garage_profile mb-3">
                <div class="item"><h4>Phone:</h4> {{ $garages->garage_phone }}</div>
                @if($garages->garage_mobile)
                    <div class="item"><h4>Mobile:</h4> {{ $garages->garage_mobile }}</div>
                @endif
                <div class="item"><h4>Email:</h4> <a href="mailto:{{ $garages->garage_email }}">{{ $garages->garage_email }}</a></div>
                @if($garages->garage_website_url)
                    <div class="item"><h4>Website:</h4> <a href="{{ $garages->garage_website_url }}" target="_blank">{{ $garages->garage_website_url }}</a></div>
                @endif
                <div class="item"><h4>Address:</h4>
                    {{ $garages->garage_street }}, {{ $garages->garage_city }},
                    {{ $garages->garage_zone }}, {{ $garages->garage_country }}
                </div>
                @if($garages->garage_garage_opening_time)
                    <div class="item"><h4>Opening Hours:</h4>
                    {{ $garages->garage_garage_opening_time }}</div>
                @endif
            </div>

            @if($garages->garage_description)
                    <div class="py-3"><h3>About:</h3> {{ $garages->garage_description }}</div>
                @endif

                @if($garages->garage_notes)
                    <div class="py-3"><h3>Notes:</h3> {{ $garages->garage_notes }}</div>
                @endif
                <!-- Google Map -->
                @if($garages->garage_google_map_link)
                    <div class="mt-4">
                        <h3>Find Us</h3>
                        <iframe
                            src="{{ $garages->garage_google_map_link }}"
                            width="100%" height="300" style="border:0; border-radius: 8px;"
                            allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                @endif
        </div>

        <!-- Right Side: Reviews & Rating Form -->
        <div class="col-lg-4">
            <div class="shadow p-4 rounded-4">
            <!-- Average Rating Summary -->
            <div class="bg-white mb-4">
                <h3>Customer Reviews</h3>
                <div class="display-6 fw-bold">{{ number_format($avgRating, 1) }} <small class="text-muted">/ 5</small></div>
                <div class="text-warning fs-4">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($avgRating))
                            ⭐
                        @else
                            ⭐
                        @endif
                    @endfor
                </div>
                <small>{{ $totalReviews }} total {{ Str::plural('review', $totalReviews) }}</small>
            </div>

            <!-- Write Review (Only for Logged-in Customers) -->
            @auth('customer')
                @if(!$hasReviewed)
                    <div class="bg-white mb-4">
                        <h3>Write a Review</h3>
                        <form action="{{ route('garage.review.submit', $garages->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Rating <span class="text-danger">*</span></label>
                                <select name="rating" class="form-control" required>
                                    <option value="">Select Stars</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Review</label>
                                <textarea name="review" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-success">
                        You already reviewed this garage with <strong>{{ $myReview->rating }} stars</strong>.
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <a href="{{ route('customer.login') }}">Log in</a> to leave a review.
                </div>
            @endauth

            <!-- Reviews List -->
            <div class="bg-white p-4 border rounded-4">
                <h3>Recent Reviews</h3>
                @if($reviews->isEmpty())
                    <p class="text-muted">No reviews yet.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($reviews as $review)
                            <li class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $review->customer->customer_name ?? 'Customer' }}</strong>
                                    <span class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '⭐' : '⭐' }}
                                        @endfor
                                    </span>
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                <p class="mt-1 mb-0">{{ $review->review }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    </div>
</div>
@endsection