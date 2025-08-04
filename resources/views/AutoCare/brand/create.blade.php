@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>{{ isset($brand) ? 'Edit Brand' : 'Add Brand' }}</h5>

    <form action="{{ isset($brand) ? route('brand.update', $brand->brand_id) : route('brand.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($brand))
            @method('PUT')
        @endif
        <div class="row">
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $brand->name ?? old('name') }}"
                required>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control"
                value="{{ $brand->slug ?? old('slug') }}" required>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="budget_type">Budget Type</label>
            <input type="text" name="budget_type" id="budget_type" class="form-control"
                value="{{ $brand->budget_type ?? old('budget_type') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="product_type">Product Type</label>
            <input type="text" name="product_type" id="product_type" class="form-control"
                value="{{ $brand->product_type ?? old('product_type') }}" required>
        </div>

        <div class="col-lg-12 col-md-12 col-12 form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="6" class="form-control">{{ $brand->description ?? old('description') }}</textarea>
        </div>
        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand/image/';
            $fallbackPath   = 'frontend/themes/default/img/brand/image/';
            $imageFile = $brand->image ?? 'sample-brand-image.png';
            $domainImageUrl = asset($imagePath . $imageFile);
            $fallbackImageUrl = asset($fallbackPath . $imageFile);
        @endphp
        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());

            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand-logo/';
            $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand_img/';

            $fallbackiconPath = 'frontend/themes/default/img/brand-logo/';
            $fallbackbannerPath = 'frontend/themes/default/img/brand_img/';

            $imageFile = $brand->image ?? 'sample-brand-image.png';
            $domainImageUrl = asset($imagePath . $imageFile);
            $fallbackImageUrl = asset($fallbackiconPath . $imageFile);


            $bannerImageFile = $brand->bannerimage ?? 'sample-bannerimage.png';
            $bannerImageUrl = asset($bannerPath . $bannerImageFile);
            $fallbackBannerImageUrl = asset($fallbackbannerPath . $bannerImageFile);

        @endphp
        <div class="col-lg-4 col-md-6 col-12 form-group">
            <label for="image">Brand Logo</label>
            <input type="file" name="image" id="image" class="form-control">
            @if (isset($brand) && $brand->image)
                <img src="{{ $domainImageUrl }}"onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $brand->name }}" style="width:100px;">            
            @endif

        </div>

        <div class="col-lg-4 col-md-6 col-12 form-group">
            <label for="bannerimage">Banner Image</label>
            <input type="file" name="bannerimage" id="bannerimage" class="form-control">
            @if (isset($brand) && $brand->bannerimage)
                    <img src="{{ $bannerImageUrl }}"  onerror="this.onerror=null;this.src='{{ $fallbackBannerImageUrl }}';" alt="{{ $brand->name ?? old('name') }}" style="width:100px; height: auto;">
            @endif
        </div>

        <div class="col-lg-4 col-md-6 col-12 form-group">
        <label for="recommended_tyre">Recommended Tyre</label>
        <!-- Hidden input to ensure a value is always sent -->
        <input type="hidden" name="recommended_tyre" value="0">
        <input type="checkbox" name="recommended_tyre" id="recommended_tyre" value="1"
            {{ isset($brand) && $brand->recommended_tyre ? 'checked' : '' }}>
    </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="promoted_text">Promoted Text</label>
            <input type="text" name="promoted_text" id="promoted_text" class="form-control"
                value="{{ $brand->promoted_text ?? old('promoted_text') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="promoted">Promoted</label>
            <input type="number" name="promoted" id="promoted" class="form-control"
                value="{{ $brand->promoted ?? old('promoted') }}" required>
        </div>
        

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ $brand->sort_order ?? old('sort_order') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="status">Status</label>
            <input type="number" name="status" id="status" class="form-control"
                value="{{ $brand->status ?? old('status') }}" required>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control"
                value="{{ $brand->meta_title ?? old('meta_title') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_keyword">Meta Keyword</label>
            <input type="text" name="meta_keyword" id="meta_keyword" class="form-control"
                value="{{ $brand->meta_keyword ?? old('meta_keyword') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_description">Meta Description</label>
            <input type="text" name="meta_description" id="meta_description" class="form-control"
                value="{{ $brand->meta_description ?? old('meta_description') }}">
        </div>

         <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="Save">&nbsp;</label>
            <button type="submit" class="btn btn-primary mt-2">Save Now</button>
        </div>  
    </div>
    </form>
    </div>
    </div>
@endsection
