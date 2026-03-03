@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <!-- Customer Menu -->
            @include('AutoCare.garages.menu')
            <!-- Profile Section -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-5">
                <div class="bg-light p-2 text-center border rounded mb-4">
                    <h5 class="m-0"><strong>Profile</strong></h5>
                </div>
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
                            <input value="{{ old('vat_number', $garages->garage_vat_number ?? '') }}" class="form-control"
                                type="text" id="vat_number" name="garage_vat_number">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="eori_number">EORI Number:</label>
                            <input value="{{ old('eori_number', $garages->garage_eori_number ?? '') }}" class="form-control"
                                type="text" id="eori_number" name="garage_eori_number">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="phone">Phone:</label>
                            <input value="{{ old('phone', $garages->garage_phone ?? '') }}" class="form-control" type="text"
                                id="phone" name="garage_phone">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="mobile">Mobile:</label>
                            <input value="{{ old('mobile', $garages->garage_mobile ?? '') }}" class="form-control"
                                type="text" id="mobile" name="garage_mobile" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="email">Email:</label>
                            <input value="{{ old('email', $garages->garage_email ?? '') }}" class="form-control"
                                type="email" id="email" name="garage_email" required>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="street">Street:</label>
                            <input value="{{ old('street', $garages->garage_street ?? '') }}" class="form-control"
                                type="text" id="street" name="garage_street">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="city">City:</label>
                            <input value="{{ old('city', $garages->garage_city ?? '') }}" class="form-control" type="text"
                                id="city" name="garage_city">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="zone">Zone:</label>
                            <input value="{{ old('zone', $garages->garage_zone ?? '') }}" class="form-control" type="text"
                                id="zone" name="garage_zone">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 form-group">
                            <label for="country">Country:</label>
                            <input value="{{ old('country', $garages->garage_country ?? '') }}" class="form-control"
                                type="text" id="country" name="garage_country">
                        </div>

                        <div class="col-lg-12 col-md-12 col-12 form-group">
                            <label for="garage_opening_time">Garage Opening Time:</label>
                            <input value="{{ old('garage_opening_time', $garages->garage_garage_opening_time ?? '') }}"
                                class="form-control" type="text" id="garage_opening_time" name="garage_garage_opening_time">
                        </div>
                        @php
                            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                            $pathPrefix = 'frontend/' . str_replace('.', '-', $domain) . '/img/garage_logo/';
                            $fallbackPrefix = 'frontend/themes/default/img/garage_logo/';
                        @endphp

                        {{-- Logo --}}
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="logo">Logo:</label>
                            <input type="file" name="garage_logo" id="garage_logo" class="form-control" accept="image/*">

                            @php
                                $logoUrl = asset($pathPrefix . $garages->garage_name. '-logo.png');
                                $fallbackLogoUrl = asset($fallbackPrefix . 'logo.png');
                            @endphp
                            <img src="{{ $logoUrl }}?v={{ time() }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackLogoUrl }}';" alt="Logo"
                                style="width:100px;">
                        </div>

                        {{-- Banner --}}
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="banner">Banner:</label>
                            <input type="file" name="garage_banner" id="banner" class="form-control" accept="image/*">
                            @php
                                $bannerUrl = asset($pathPrefix . 'home-banner.jpg');
                                $fallbackBannerUrl = asset($fallbackPrefix . 'home-banner.jpg');
                            @endphp
                            <img src="{{ $bannerUrl }}?v={{ time() }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackBannerUrl }}';" alt="Banner"
                                style="width:180px;height:75px;">
                        </div>

                        {{-- Favicon --}}
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="favicon">Favicon:</label>
                            <input type="file" name="garage_favicon" id="favicon" class="form-control" accept="image/*">
                            @php
                                $faviconUrl = asset($pathPrefix . 'favicon.png');
                                $fallbackFaviconUrl = asset($fallbackPrefix . 'favicon.png');
                            @endphp
                            <img src="{{ $faviconUrl }}?v={{ time() }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackFaviconUrl }}';" alt="Favicon"
                                style="width:32px;">
                        </div>

                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="social_facebook">Facebook:</label>
                            <input value="{{ old('social_facebook', $garages->garage_social_facebook ?? '') }}"
                                class="form-control" type="url" id="social_facebook" name="garage_social_facebook">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="social_instagram">Instagram:</label>
                            <input value="{{ old('social_instagram', $garages->garage_social_instagram ?? '') }}"
                                class="form-control" type="url" id="social_instagram" name="garage_social_instagram">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="social_twitter">Twitter:</label>
                            <input value="{{ old('social_twitter', $garages->garage_social_twitter ?? '') }}"
                                class="form-control" type="url" id="social_twitter" name="garage_social_twitter">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="social_youtube">Youtube:</label>
                            <input value="{{ old('social_youtube', $garages->garage_social_youtube ?? '') }}"
                                class="form-control" type="url" id="social_youtube" name="garage_social_youtube">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="google_map_link">Google Map Link:</label>
                            <input value="{{ old('google_map_link', $garages->garage_google_map_link ?? '') }}"
                                class="form-control" type="url" id="google_map_link" name="garage_google_map_link">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="longitude">Longitude:</label>
                            <input value="{{ old('longitude', $garages->garage_longitude ?? '') }}" class="form-control"
                                type="text" id="longitude" name="garage_longitude">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="latitude">Latitude:</label>
                            <input value="{{ old('latitude', $garages->garage_latitude ?? '') }}" class="form-control"
                                type="text" id="latitude" name="garage_latitude">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="google_reviews_link">Google Reviews Link:</label>
                            <input value="{{ old('google_reviews_link', $garages->garage_google_reviews_link ?? '') }}"
                                class="form-control" type="url" id="google_reviews_link" name="garage_google_reviews_link">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="google_reviews_stars">Google Reviews Stars:</label>
                            <input value="{{ old('google_reviews_stars', $garages->garage_google_reviews_stars ?? '') }}"
                                class="form-control" type="text" id="google_reviews_stars"
                                name="garage_google_reviews_stars">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="google_reviews_count">Google Reviews Count:</label>
                            <input value="{{ old('google_reviews_count', $garages->garage_google_reviews_count ?? '') }}"
                                class="form-control" type="text" id="google_reviews_count"
                                name="garage_google_reviews_count">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="website_url">Website URL:</label>
                            <input value="{{ old('website_url', $garages->garage_website_url ?? '') }}" class="form-control"
                                type="url" id="website_url" name="garage_website_url">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="status">Status</label>
                            <select name="garage_status" id="status" class="form-control">
                                <option value="1" {{ isset($garages) && $garages->garage_status == 1 ? 'selected' : '' }}>
                                    Active</option>
                                <option value="0" {{ isset($garages) && $garages->garage_status == 0 ? 'selected' : '' }}>
                                    Inactive</option>
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

                                // Get selected order types from old input or database
                                $selectedOrderTypes = old('garage_order_types', isset($garages) ? explode(',', $garages->garage_order_types ?? '') : []);

                                // Always ensure 'fully_fitted' is selected by default if nothing is selected
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

                    <!-- Commission Details -->
<div class="bg-light p-2 text-center border rounded mb-4">
    <h5 class="m-0"><strong>Commission Details</strong></h5>
</div>

<div class="col-lg-2 col-md-6 col-12 form-group">
    <label for="fitting_charges">Fitting Charge <span class="text-danger">*</span></label>
    <input
        type="number"
        step="0.01"
        class="form-control"
        id="fitting_charges"
        name="fitting_charges"
        value="{{ old('fitting_charges', $garages->fitting_charges ?? '') }}"
        required>
</div>

<div class="col-lg-2 col-md-6 col-12 form-group">
    <label for="garage_fitting_vat_class">
        Garage Fitting VAT <span class="text-danger">*</span>
    </label>

    <select class="form-control" 
            id="garage_fitting_vat_class" 
            name="garage_fitting_vat_class" 
            required>
        <option value="9"
            {{ old('garage_fitting_vat_class', $garages->garage_fitting_vat_class ?? '') == 9 ? 'selected' : '' }}>
            Inc VAT (20%)
        </option>
         <option value="0"
            {{ old('garage_fitting_vat_class', $garages->garage_fitting_vat_class ?? '') == 0 ? 'selected' : '' }}>
            Exc VAT (0%)
        </option>

    </select>
</div>

<div class="col-lg-2 col-md-6 col-12 form-group">
    <label for="commission_type">Commission Type <span class="text-danger">*</span></label>
    <select
        class="form-control"
        id="commission_type"
        name="commission_type"
        required>
        <option value="">Select Commission Type</option>
        <option value="Fixed"
            {{ old('commission_type', $garages->commission_type ?? '') === 'Fixed' ? 'selected' : '' }}>
            Fixed
        </option>
        <option value="Percentage"
            {{ old('commission_type', $garages->commission_type ?? '') === 'Percentage' ? 'selected' : '' }}>
            Percentage
        </option>
    </select>
</div>

<div class="col-lg-2 col-md-6 col-12 form-group">
    <label for="commission_price">Commission Price <span class="text-danger">*</span></label>
    <input
        type="number"
        step="0.01"
        class="form-control"
        id="commission_price"
        name="commission_price"
        value="{{ old('commission_price', $garages->commission_price ?? '') }}"
        required>
</div>

<div class="col-lg-2 col-md-6 col-12 form-group">
    <label for="card_processing_fee">Card Processing Fee (%) <span class="text-danger">*</span></label>
    <input
        type="number"
        step="0.01"
        class="form-control"
        id="card_processing_fee"
        name="card_processing_fee"
        value="{{ old('card_processing_fee', $garages->card_processing_fee ?? '') }}"
        required>
</div>

                </div>


                    <div class="text-right"><button class="btn btn-primary mt-2"
                            type="submit">{{ isset($garages) ? 'Update' : 'Save' }}</button></div>

                </form>
            </div>
            <div class="mb-5">
                <div class="bg-light p-2 text-center border rounded mb-4 d-flex justify-content-between align-items-center">
                    <h5 class="m-0"><strong>Services Offered by {{ $garages->garage_name }}</strong></h5>
                     <a href="{{ route('services.create') }}?garage_id={{ $garages->id }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"></i> Add New Service
                     </a>
                </div>

                @if(isset($services) && $services->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Service Name</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Price Type</th>
                                    <th scope="col">Cost Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <th scope="row">{{ $service->service_id }}</th>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->slug }}</td>
                                        <td>
                                            @if($service->price_type == 'fixed-price')
                                                <span class="badge bg-primary">Fixed Price</span>
                                            @elseif($service->price_type == 'call-now')
                                                <span class="badge bg-warning">Call Now</span>
                                            @elseif($service->price_type == 'quote-now')
                                                <span class="badge bg-info">Quote Now</span>
                                            @elseif($service->price_type == 'free')
                                                <span class="badge bg-success">Free</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($service->cost_price, 2) }}</td>
                                        <td>
                                            @if($service->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                             <div class="btn-group" role="group">
                                                <a href="{{ route('services.edit', $service->service_id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                 <form action="{{ route('services.destroy', $service->service_id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <p>No services found for this garage.</p>
                        <a href="{{ route('services.create') }}?garage_id={{ $garages->id }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create the First Service
                        </a>
                    </div>
                @endif
            </div>
            <!-- Password Update Section -->
            @if(isset($garages))
                <div class="mb-5">
                    <div class="bg-light p-2 text-center border rounded mb-4">
                        <h5 class="m-0"><strong>Update Password</strong></h5>
                    </div>
                    <form action="{{ route('AutoCare.garages.update.password', $garages->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required
                                    minlength="8">
                                @error('new_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="new_password_confirmation">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                    class="form-control" required minlength="8">
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success mt-2" type="submit">Update Password</button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
@endsection