@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
        <h5>{{ isset($service) ? 'Edit Service' : 'Add Service' }}</h5>
        <form action="{{ isset($service) ? route('services.update', $service->service_id) : route('services.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($service))
                @method('PUT')
            @endif
            <div class="row">
                <!-- Name Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ $service->name ?? old('name') }}" required>
                </div>

                <!-- Service Lead Time Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="service_lead_time">Service Lead Time</label>
                    <input type="text" name="service_lead_time" id="service_lead_time" class="form-control"
                        value="{{ $service->service_lead_time ?? old('service_lead_time') }}">
                </div>

                <!-- Slug Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="slug">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control"
                        value="{{ $service->slug ?? old('slug') }}">
                </div>

                <!-- Long Description Field -->
                <div class="col-12 form-group">
                    <label for="long_description">Content</label>
                    <textarea name="content" id="content"
                        class="form-control">{{ $service->content ?? old('content') }}</textarea>
                </div>

                <!-- Image Field -->
                    @php
                        $domain = str_replace(['http://', 'https://'], '', request()->getHost());

                        $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon/';
                        $innerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img/';
                        $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-banners/';

                        $fallbackiconPath   = 'frontend/themes/default/img/service/';
                        $fallbackinnerPath  = 'frontend/themes/default/img/service-inner-img/';
                        $fallbackbannerPath = 'frontend/themes/default/img/service-banners/';

                        $imageFile = $service->image ?? 'sample-icon-image.png';
                        $domainImageUrl = asset($imagePath . $imageFile);
                        $fallbackImageUrl = asset($fallbackiconPath . $imageFile);

                        $innerImageFile = $service->inner_image ?? 'sample-inner-image.png';
                        $innerImageUrl = asset($innerPath . $innerImageFile);
                        $fallbackInnerImageUrl = asset($fallbackinnerPath . $innerImageFile);

                        $bannerImageFile = $service->service_banner_path ?? 'sample-banner-image.png';
                        $bannerImageUrl = asset($bannerPath . $bannerImageFile);
                        $fallbackBannerImageUrl = asset($fallbackbannerPath . $bannerImageFile);

                    @endphp

                    <div class="col-lg-6 col-md-6 col-12 form-group">
                        <label for="image">Service icon</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        @if (!empty($service->image))
                            <img src="{{ $domainImageUrl }}"onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $service->name ?? old('name') }}" style="width:100px;">
                        @endif

                    </div>

                    <!-- inner_image Field -->
                    <div class="col-lg-6 col-md-6 col-12 form-group">
                        <label for="iinner_image">Service Image</label>
                        <input type="file" name="inner_image" id="inner_image" class="form-control" accept="inner_image/*">
                        @if (isset($service->inner_image))
                    <img src="{{ $innerImageUrl }}"onerror="this.onerror=null;this.src='{{ $fallbackInnerImageUrl }}';" alt="{{ $service->name ?? old('name') }}" style="width:100px;">
                            <!-- <img src="{{ !empty($imagePath) ? $domainImageUrl : $fallbackImageUrl }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $service->name }}" style="width: 100px;"> -->
                        @endif
                    </div>

                    <!-- Service Banner Path Field -->
                    <div class="col-lg-6 col-md-6 col-12 form-group">
                        <label for="service_banner_path">Service Banner</label>
                        <input type="file" name="service_banner_path" id="service_banner_path" class="form-control"
                            accept="image/*">
                        @if (isset($service->service_banner_path))
                            <img src="{{ $bannerImageUrl }}"onerror="this.onerror=null;this.src='{{ $fallbackBannerImageUrl }}';" alt="{{ $service->name ?? old('name') }}" style="width:100px;">
                        @endif
                    </div>

                <!-- Price Fields -->
                {{-- <div class="col-lg-6 col-md-6 col-12 form-group">
                    <label for="price_1.6L">Price for 1.6L</label>
                    <input type="text" name="price_1.6L" id="price_1.6L" class="form-control"
                        value="{{ $service->price_1.6L ?? old('price_1.6L') }}" required>
                </div>

                <div class="col-lg-6 col-md-6 col-12 form-group">
                    <label for="price_1.6_2.2L">Price for 1.6-2.2L</label>
                    <input type="text" name="price_1.6_2.2L" id="price_1.6_2.2L" class="form-control"
                        value="{{ $service->price_1.6_2.2L ?? old('price_1.6_2.2L') }}" required>
                </div> --}}

                <!-- Display Status Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="display_status">Display Status</label>
                    <select name="display_status" id="display_status" class="form-control" required>
                        <option value="1" {{ isset($service) && $service->display_status == 1 ? 'selected' : '' }}>
                            Displayed</option>
                        <option value="0" {{ isset($service) && $service->display_status == 0 ? 'selected' : '' }}>Hidden
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tax_class_id">Vat:</label>
                    <select name="tax_class_id" id="tax_class_id" class="form-control" required>
                        <option value="9" {{ isset($service) && $service->tax_class_id == 9 ? 'selected' : '' }}>Vat
                        </option>
                        <option value="0" {{ isset($service) && $service->tax_class_id == 0 ? 'selected' : '' }}>No Vat
                        </option>
                    </select>
                </div>
                <!-- Status Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ isset($service) && $service->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ isset($service) && $service->status == 0 ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <!-- Sort Order Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control"
                        value="{{ $service->sort_order ?? old('sort_order') }}" required>
                </div>

                <!-- Cost Price Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="cost_price">Cost Price</label>
                    <input type="number" name="cost_price" id="cost_price" class="form-control"
                        value="{{ $service->cost_price ?? old('cost_price') }}" required step="0.01" min="0">
                </div>

                <!-- Meta Title Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                        value="{{ $service->meta_title ?? old('meta_title') }}">
                </div>

                <!-- Meta Keywords Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                        value="{{ $service->meta_keywords ?? old('meta_keywords') }}">
                </div>

                <!-- Meta Description Field -->
                <div class="col-lg-6 col-md-6 col-12 form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea name="meta_description" id="meta_description"
                        class="form-control">{{ $service->meta_description ?? old('meta_description') }}</textarea>
                </div>

                <!-- Robots Noindex Follow Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="robots_noindex_follow">Robots Noindex Follow</label>
                    <select name="robots_noindex_follow" id="robots_noindex_follow" class="form-control">
                        <option value="1" {{ isset($service) && $service->robots_noindex_follow == 1 ? 'selected' : '' }}>
                            Yes</option>
                        <option value="0" {{ isset($service) && $service->robots_noindex_follow == 0 ? 'selected' : '' }}>
                            No</option>
                    </select>
                </div>

                <!-- Exclude Sitemap Field -->
                <div class="col-lg-3 col-md-6 col-12 form-group">
                    <label for="exclude_sitemap">Exclude from Sitemap</label>
                    <select name="exclude_sitemap" id="exclude_sitemap" class="form-control" required>
                        <option value="1" {{ isset($service) && $service->exclude_sitemap == 1 ? 'selected' : '' }}>Yes
                        </option>
                        <option value="0" {{ isset($service) && $service->exclude_sitemap == 0 ? 'selected' : '' }}>No
                        </option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Save</button>
        </form>
    </div>
</div>
@endsection