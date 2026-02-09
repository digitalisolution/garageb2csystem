@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form
                action="{{ isset($garages) ? route('AutoCare.garages.update', $garages->id) : route('AutoCare.garages.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($garages))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_name">Garage Name:</label>
                        <input class="form-control" type="text" id="garage_name" name="garage_name"
                            value="{{ old('garage_name', $garages->garage_name ?? '') }}" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="company_number">Company Number:</label>
                        <input class="form-control" type="text" id="company_number" name="garage_company_number"
                            value="{{ old('company_number', $garages->garage_company_number ?? '') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="vat_number">Vat Number:</label>
                        <input value="{{ old('vat_number', $garages->garage_vat_number ?? '') }}" class="form-control" type="text"
                            id="vat_number" name="garage_vat_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="eori_number">EORI Number:</label>
                        <input value="{{ old('eori_number', $garages->garage_eori_number ?? '') }}" class="form-control" type="text"
                            id="eori_number" name="garage_eori_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="phone">Phone:</label>
                        <input value="{{ old('phone', $garages->garage_phone ?? '') }}" class="form-control" type="text" id="phone"
                            name="garage_phone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="mobile">Mobile:</label>
                        <input value="{{ old('mobile', $garages->garage_mobile ?? '') }}" class="form-control" type="text"
                            id="mobile" name="garage_mobile" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="email">Email:</label>
                        <input value="{{ old('email', $garages->garage_email ?? '') }}" class="form-control" type="email" id="email"
                            name="garage_email" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="street">Street:</label>
                        <input value="{{ old('street', $garages->garage_street ?? '') }}" class="form-control" type="text"
                            id="street" name="garage_street">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="city">City:</label>
                        <input value="{{ old('city', $garages->garage_city ?? '') }}" class="form-control" type="text" id="city"
                            name="garage_city">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="zone">Zone:</label>
                        <input value="{{ old('zone', $garages->garage_zone ?? '') }}" class="form-control" type="text" id="zone"
                            name="garage_zone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="country">Country:</label>
                        <input value="{{ old('country', $garages->garage_country ?? '') }}" class="form-control" type="text"
                            id="country" name="garage_country">
                    </div>

                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        <label for="garage_opening_time">Garage Opening Time:</label>
                        <input value="{{ old('garage_opening_time', $garages->garage_garage_opening_time ?? '') }}"
                            class="form-control" type="text" id="garage_opening_time" name="garage_garage_opening_time">
                    </div>
                    @php
                        $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                        $pathPrefix = 'frontend/' . str_replace('.', '-', $domain) . '/img/logo/';
                        $fallbackPrefix = 'frontend/themes/default/img/logo/';
                    @endphp

                    {{-- Logo --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="logo">Logo:</label>
                        <input type="file" name="garage_logo" id="logo" class="form-control" accept="image/*">

                        @php
                            $logoUrl = asset($pathPrefix . 'logo.png');
                            $fallbackLogoUrl = asset($fallbackPrefix . 'logo.png');
                        @endphp
                        <img src="{{ $logoUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackLogoUrl }}';" 
                             alt="Logo" style="width:100px;">
                    </div>

                    {{-- Banner --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="banner">Banner:</label>
                        <input type="file" name="garage_banner" id="banner" class="form-control" accept="image/*">
                        @php
                            $bannerUrl = asset($pathPrefix . 'home-banner.jpg');
                            $fallbackBannerUrl = asset($fallbackPrefix . 'home-banner.jpg');
                        @endphp
                        <img src="{{ $bannerUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackBannerUrl }}';" 
                             alt="Banner" style="width:180px;height:75px;">
                    </div>

                    {{-- Favicon --}}
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="favicon">Favicon:</label>
                        <input type="file" name="garage_favicon" id="favicon" class="form-control" accept="image/*">
                        @php
                            $faviconUrl = asset($pathPrefix . 'favicon.png');
                            $fallbackFaviconUrl = asset($fallbackPrefix . 'favicon.png');
                        @endphp
                        <img src="{{ $faviconUrl }}?v={{ time() }}" onerror="this.onerror=null;this.src='{{ $fallbackFaviconUrl }}';" 
                             alt="Favicon" style="width:32px;">
                    </div>

                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_facebook">Facebook:</label>
                        <input value="{{ old('social_facebook', $garages->garage_social_facebook ?? '') }}" class="form-control"
                            type="url" id="social_facebook" name="garage_social_facebook">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_instagram">Instagram:</label>
                        <input value="{{ old('social_instagram', $garages->garage_social_instagram ?? '') }}" class="form-control"
                            type="url" id="social_instagram" name="garage_social_instagram">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_twitter">Twitter:</label>
                        <input value="{{ old('social_twitter', $garages->garage_social_twitter ?? '') }}" class="form-control"
                            type="url" id="social_twitter" name="garage_social_twitter">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_youtube">Youtube:</label>
                        <input value="{{ old('social_youtube', $garages->garage_social_youtube ?? '') }}" class="form-control"
                            type="url" id="social_youtube" name="garage_social_youtube">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_map_link">Google Map Link:</label>
                        <input value="{{ old('google_map_link', $garages->garage_google_map_link ?? '') }}" class="form-control"
                            type="url" id="google_map_link" name="garage_google_map_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="longitude">Longitude:</label>
                        <input value="{{ old('longitude', $garages->garage_longitude ?? '') }}" class="form-control" type="text"
                            id="longitude" name="garage_longitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="latitude">Latitude:</label>
                        <input value="{{ old('latitude', $garages->garage_latitude ?? '') }}" class="form-control" type="text"
                            id="latitude" name="garage_latitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_link">Google Reviews Link:</label>
                        <input value="{{ old('google_reviews_link', $garages->garage_google_reviews_link ?? '') }}"
                            class="form-control" type="url" id="google_reviews_link" name="garage_google_reviews_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_stars">Google Reviews Stars:</label>
                        <input value="{{ old('google_reviews_stars', $garages->garage_google_reviews_stars ?? '') }}"
                            class="form-control" type="text" id="google_reviews_stars" name="garage_google_reviews_stars">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_count">Google Reviews Count:</label>
                        <input value="{{ old('google_reviews_count', $garages->garage_google_reviews_count ?? '') }}"
                            class="form-control" type="text" id="google_reviews_count" name="garage_google_reviews_count">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="website_url">Website URL:</label>
                        <input value="{{ old('website_url', $garages->garage_website_url ?? '') }}" class="form-control" type="url"
                            id="website_url" name="garage_website_url">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="status">Status</label>
                        <select name="garage_status" id="status" class="form-control">
                            <option value="1" {{ isset($garages) && $garages->garage_status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ isset($garages) && $garages->garage_status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="description">Description</label>
                        <textarea name="garage_description" id="description"
                            class="form-control">{{ $garages->garage_description ?? old('description') }}</textarea>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="notes">Notes:</label>
                        <textarea name="garage_notes" id="notes"
                            class="form-control">{{ $garages->garage_notes ?? old('notes') }}</textarea>
                    </div>
                        <!-- Order Types Section -->
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
                    <!-- Bank Details -->
                <div class="bg-light p-2 text-center border rounded mb-4">
                    <h5 class="m-0"><strong>Bank Details</strong></h5>
                </div>

                <div class="col-lg-4 col-md-6 col-12 form-group">
                    <label for="garage_bank_name">Bank Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="garage_bank_name"
                        name="garage_bank_name"
                        value="{{ old('garage_bank_name', $garages->garage_bank_name ?? '') }}">
                </div>

                <div class="col-lg-4 col-md-6 col-12 form-group">
                    <label for="garage_bank_sort_code">Sort Code</label>
                    <input
                        type="text"
                        class="form-control"
                        id="garage_bank_sort_code"
                        name="garage_bank_sort_code"
                        value="{{ old('garage_bank_sort_code', $garages->garage_bank_sort_code ?? '') }}">
                </div>

                <div class="col-lg-4 col-md-6 col-12 form-group">
                    <label for="garage_account_number">Account Number</label>
                    <input
                        type="text"
                        class="form-control"
                        id="garage_account_number"
                        name="garage_account_number"
                        value="{{ old('garage_account_number', $garages->garage_account_number ?? '') }}">
                </div>

                <div class="col-lg-4 col-md-6 col-12 form-group">
                    <label for="garage_revolut_source_id">Revolut Source ID</label>
                    <input
                        type="text"
                        class="form-control"
                        id="garage_revolut_source_id"
                        name="garage_revolut_source_id"
                        value="{{ old('garage_revolut_source_id', $garages->garage_revolut_source_id ?? '') }}">
                </div>

                <div class="col-lg-4 col-md-6 col-12 form-group">
                    <label for="garage_revoult_counterparty_id">Revolut Counterparty ID</label>
                    <input
                        type="text"
                        class="form-control"
                        id="garage_revoult_counterparty_id"
                        name="garage_revoult_counterparty_id"
                        value="{{ old('garage_revoult_counterparty_id', $garages->garage_revoult_counterparty_id ?? '') }}">
                </div>

                </div>
                <div class="text-right"><button class="btn btn-primary mt-2"
                        type="submit">{{ isset($garages) ? 'Update' : 'Save' }}</button></div>

            </form>
        </div>
    </div>
@endsection