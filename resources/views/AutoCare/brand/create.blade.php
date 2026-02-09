@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>{{ isset($brand) ? 'Edit Brand' : 'Add Brand' }}</h5>
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
            <select name="budget_type" id="budget_type" class="form-control" required>
                <option value="budget" {{ isset($brand) && $brand->budget_type == 'budget' ? 'selected' : '' }}>Budget</option>
                <option value="mid-range" {{ isset($brand) && $brand->budget_type == 'mid-range' ? 'selected' : '' }}>Mid-Range</option>
                 <option value="premium" {{ isset($brand) && $brand->budget_type == 'premium' ? 'selected' : '' }}>Premium</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="product_type">Product Type</label>
            <input type="text" name="product_type" id="product_type" class="form-control" value="{{ $brand->product_type ?? 'Tyre' ?? old('product_type') }}" required>
        </div>

        <div class="col-lg-12 col-md-12 col-12 form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="6" class="form-control">{{ $brand->description ?? old('description') }}</textarea>
        </div>
        
        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());

            $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/brand_img/';
            $fallbackBannerPath = 'frontend/themes/default/img/brand_img/';
            $bannerImageFile = $brand->bannerimage ?? 'sample-bannerimage.png';
            $bannerImageUrl = asset($bannerPath . $bannerImageFile);
            $fallbackBannerImageUrl = asset($fallbackBannerPath . $bannerImageFile);

            $logoFile = $brand->image ?? '';
            $cdnLogoBase = config('cdn.brandlogo_cdn_url');
            $cdnLogoUrl = $cdnLogoBase . $logoFile;
            $localLogoUrl = asset('frontend/themes/default/img/brand-logo/' . $logoFile);
            $defaultLogoUrl = asset('frontend/themes/default/img/brand-logo/no-image.png');
        @endphp

        <div class="col-lg-4 col-md-6 col-12 form-group">
            <label for="image">Brand Logo</label>
            <input type="file" name="image" id="image" class="form-control">
            @if (!empty($logoFile))
                <img src="{{ $cdnLogoUrl }}"
                     onerror="this.onerror=null;
                              this.src='{{ $localLogoUrl }}';
                              this.onerror=function(){this.src='{{ $defaultLogoUrl }}';}"
                     alt="{{ $brand->name ?? 'Brand Logo' }}"
                     style="width:100px;">
            @else
                <img src="{{ $defaultLogoUrl }}"
                     alt="No Brand Logo"
                     style="width:100px;">
            @endif

        </div>

        <div class="col-lg-4 col-md-6 col-12 form-group">
            <label for="bannerimage">Banner Image</label>
            <input type="file" name="bannerimage" id="bannerimage" class="form-control">
            @if (!empty($brand->bannerimage))
                <img src="{{ $bannerImageUrl }}" onerror="this.onerror=null; this.src='{{ $fallbackBannerImageUrl }}';"
                     alt="{{ $brand->name ?? old('name') }}" style="width:100px; height:auto;">
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
            <select name="promoted" id="promoted" class="form-control" required>
                <option value="1" {{ isset($brand) && $brand->promoted == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ isset($brand) && $brand->promoted == 0 ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ $brand->sort_order ?? old('sort_order') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="1" {{ isset($brand) && $brand->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ isset($brand) && $brand->status == 0 ? 'selected' : '' }}>Inactive
                </option>
            </select>
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
