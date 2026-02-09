@extends('layouts.app')

@section('content')
	<div class="pt-60 pb-60">
		<div class="container">
			<!-- garage Menu -->
			@include('garage.menu')
			<!-- Profile Section -->
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Profile</strong></h2>
				</div>
				<form action="{{ route('garage.update-profile') }}" method="POST">
					@csrf
					<div class="row">
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_name">Garage Name:</label>
                        <input class="form-control" type="text" id="garage_name" name="garage_name"
                            value="{{ old('garage_name', $garages->garage_name ?? '') }}" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_company_number">Company Number:</label>
                        <input class="form-control" type="text" id="garage_company_number" name="garage_company_number"
                            value="{{ old('garage_company_number', $garages->garage_company_number ?? '') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_vat_number">VAT Number:</label>
                        <input value="{{ old('garage_vat_number', $garages->garage_vat_number ?? '') }}" class="form-control" type="text"
                            id="garage_vat_number" name="garage_vat_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_eori_number">EORI Number:</label>
                        <input value="{{ old('garage_eori_number', $garages->garage_eori_number ?? '') }}" class="form-control" type="text"
                            id="garage_eori_number" name="garage_eori_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_phone">Phone:</label>
                        <input value="{{ old('garage_phone', $garages->garage_phone ?? '') }}" class="form-control" type="text" id="garage_phone"
                            name="garage_phone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_mobile">Mobile:</label>
                        <input value="{{ old('garage_mobile', $garages->garage_mobile ?? '') }}" class="form-control" type="text"
                            id="garage_mobile" name="garage_mobile" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_email">Email:</label>
                        <input value="{{ old('garage_email', $garages->garage_email ?? '') }}" class="form-control" type="email" id="garage_email"
                            name="garage_email" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_street">Street:</label>
                        <input value="{{ old('garage_street', $garages->garage_street ?? '') }}" class="form-control" type="text"
                            id="garage_street" name="garage_street">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_city">City:</label>
                        <input value="{{ old('garage_city', $garages->garage_city ?? '') }}" class="form-control" type="text" id="garage_city"
                            name="garage_city">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_zone">Zone:</label>
                        <input value="{{ old('garage_zone', $garages->garage_zone ?? '') }}" class="form-control" type="text" id="garage_zone"
                            name="garage_zone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_country">Country:</label>
                        <input value="{{ old('garage_country', $garages->garage_country ?? '') }}" class="form-control" type="text"
                            id="garage_country" name="garage_country">
                    </div>

                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        <label for="garage_garage_opening_time">Garage Opening Time:</label>
                        <input value="{{ old('garage_garage_opening_time', $garages->garage_garage_opening_time ?? '') }}"
                            class="form-control" type="text" id="garage_garage_opening_time" name="garage_garage_opening_time">
                    </div>
                    
                    @php
                        $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                        $pathPrefix = 'frontend/' . str_replace('.', '-', $domain) . '/img/garage_logo/';
                        $fallbackPrefix = 'frontend/themes/default/img/garage_logo/';
                    @endphp

                    {{-- Logo --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_logo">Logo:</label>
                        <input type="file" name="garage_logo" id="garage_logo" class="form-control" accept="image/*">

                        @if($garages->garage_logo)
                            @php
                                $logoUrl = asset($pathPrefix . $garages->garage_logo);
                                $fallbackLogoUrl = asset($fallbackPrefix . $garages->garage_logo);
                            @endphp
                            <img src="{{ $logoUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackLogoUrl }}';" 
                                 alt="Logo" style="width:100px;">
                        @endif
                    </div>

                    {{-- Banner --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_banner">Banner:</label>
                        <input type="file" name="garage_banner" id="garage_banner" class="form-control" accept="image/*">
                        @if($garages->garage_banner)
                            @php
                                $bannerUrl = asset($pathPrefix . $garages->garage_banner);
                                $fallbackBannerUrl = asset($fallbackPrefix . $garages->garage_banner);
                            @endphp
                            <img src="{{ $bannerUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackBannerUrl }}';" 
                                 alt="Banner" style="width:180px;height:75px;">
                        @endif
                    </div>

                    {{-- Favicon --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_favicon">Favicon:</label>
                        <input type="file" name="garage_favicon" id="garage_favicon" class="form-control" accept="image/*">
                        @if($garages->garage_favicon)
                            @php
                                $faviconUrl = asset($pathPrefix . $garages->garage_favicon);
                                $fallbackFaviconUrl = asset($fallbackPrefix . $garages->garage_favicon);
                            @endphp
                            <img src="{{ $faviconUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackFaviconUrl }}';" 
                                 alt="Favicon" style="width:32px;">
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_social_facebook">Facebook:</label>
                        <input value="{{ old('garage_social_facebook', $garages->garage_social_facebook ?? '') }}" class="form-control"
                            type="url" id="garage_social_facebook" name="garage_social_facebook">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_social_instagram">Instagram:</label>
                        <input value="{{ old('garage_social_instagram', $garages->garage_social_instagram ?? '') }}" class="form-control"
                            type="url" id="garage_social_instagram" name="garage_social_instagram">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_social_twitter">Twitter:</label>
                        <input value="{{ old('garage_social_twitter', $garages->garage_social_twitter ?? '') }}" class="form-control"
                            type="url" id="garage_social_twitter" name="garage_social_twitter">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_social_youtube">YouTube:</label>
                        <input value="{{ old('garage_social_youtube', $garages->garage_social_youtube ?? '') }}" class="form-control"
                            type="url" id="garage_social_youtube" name="garage_social_youtube">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_google_map_link">Google Map Link:</label>
                        <input value="{{ old('garage_google_map_link', $garages->garage_google_map_link ?? '') }}" class="form-control"
                            type="url" id="garage_google_map_link" name="garage_google_map_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_longitude">Longitude:</label>
                        <input value="{{ old('garage_longitude', $garages->garage_longitude ?? '') }}" class="form-control" type="text"
                            id="garage_longitude" name="garage_longitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_latitude">Latitude:</label>
                        <input value="{{ old('garage_latitude', $garages->garage_latitude ?? '') }}" class="form-control" type="text"
                            id="garage_latitude" name="garage_latitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_google_reviews_link">Google Reviews Link:</label>
                        <input value="{{ old('garage_google_reviews_link', $garages->garage_google_reviews_link ?? '') }}"
                            class="form-control" type="url" id="garage_google_reviews_link" name="garage_google_reviews_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_google_reviews_stars">Google Reviews Stars:</label>
                        <input value="{{ old('garage_google_reviews_stars', $garages->garage_google_reviews_stars ?? '') }}"
                            class="form-control" type="text" id="garage_google_reviews_stars" name="garage_google_reviews_stars">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_google_reviews_count">Google Reviews Count:</label>
                        <input value="{{ old('garage_google_reviews_count', $garages->garage_google_reviews_count ?? '') }}"
                            class="form-control" type="text" id="garage_google_reviews_count" name="garage_google_reviews_count">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="garage_website_url">Website URL:</label>
                        <input value="{{ old('garage_website_url', $garages->garage_website_url ?? '') }}" class="form-control" type="url"
                            id="garage_website_url" name="garage_website_url">
                    </div>
                    
                    <div class="col-lg-6 col-md-6 col-12 form-group">
                        <label for="garage_description">Description</label>
                        <textarea name="garage_description" id="garage_description"
                            class="form-control">{{ old('garage_description', $garages->garage_description ?? '') }}</textarea>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12 form-group">
                        <label for="garage_notes">Notes:</label>
                        <textarea name="garage_notes" id="garage_notes"
                            class="form-control">{{ old('garage_notes', $garages->garage_notes ?? '') }}</textarea>
                    </div>
                        <div class="col-12 form-group mt-4">
                            <label><strong>Select Order Types:</strong></label>
                            <br>

                            @php
                                $orderTypes = [
                                    'fully_fitted' => 'Fully Fitted',
                                    'mobile_fitted' => 'Mobile Fitted',
                                    'mailorder' => 'Mail Order',
                                    'delivery' => 'Delivery',
                                    'collection' => 'Collection',
                                ];

                                $selectedOrderTypes = old('garage_order_types', isset($garages) ? explode(',', $garages->garage_order_types ?? '') : []);
                                if (empty($selectedOrderTypes)) {
                                    $selectedOrderTypes = ['fully_fitted'];
                                }
                            @endphp

                            @foreach ($orderTypes as $key => $label)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="garage_order_types[]"
                                        id="order_type_{{ $key }}" value="{{ $key }}" {{ in_array($key, $selectedOrderTypes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="order_type_{{ $key }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                </div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Profile</button></div>
				</form>
			</div>

			<!-- Password Update Section -->
			<div class="short__item mb-4">
				<div class="bg-gray p-3 text-center rounded mb-4">
					<h2 class="m-0"><strong>Update Password</strong></h2>
				</div>
				<form action="{{ route('garage.update-password') }}" method="POST">
					@csrf
					<div class="row">
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label>Current Password</label>
								<input type="password" name="current_password" class="form-control" required minlength="8">
								@error('current_password')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label>New Password</label>
								<input type="password" name="new_password" class="form-control" required minlength="8">
								@error('new_password')
									<div class="text-danger">{{ $message }}</div>
								@enderror
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label>Confirm New Password</label>
								<input type="password" name="new_password_confirmation" class="form-control" required minlength="8">
							</div>
						</div>
					</div>
					<div class="text-center mt-3"><button type="submit" class="btn btn-theme-select">Update Password</button></div>
				</form>
			</div>
		</div>
	</div>
@endsection