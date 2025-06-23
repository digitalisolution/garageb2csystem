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
            <label for="promoted">Promoted</label>
            <input type="number" name="promoted" id="promoted" class="form-control"
                value="{{ $brand->promoted ?? old('promoted') }}" required>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="promoted_text">Promoted Text</label>
            <input type="text" name="promoted_text" id="promoted_text" class="form-control"
                value="{{ $brand->promoted_text ?? old('promoted_text') }}">
        </div>

        <div class="col-lg-12 col-md-12 col-12 form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $brand->description ?? old('description') }}</textarea>
        </div>


        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="budget_type">Budget Type</label>
            <input type="text" name="budget_type" id="budget_type" class="form-control"
                value="{{ $brand->budget_type ?? old('budget_type') }}">
        </div>

     <div class="col-lg-3 col-md-6 col-12 form-group">
    <label for="recommended_tyre">Recommended Tyre</label>
    
    <!-- Hidden input to ensure a value is always sent -->
    <input type="hidden" name="recommended_tyre" value="0">
    
    <input type="checkbox" name="recommended_tyre" id="recommended_tyre" value="1"
        {{ isset($brand) && $brand->recommended_tyre ? 'checked' : '' }}>
</div>


        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ $brand->sort_order ?? old('sort_order') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="product_type">Product Type</label>
            <input type="text" name="product_type" id="product_type" class="form-control"
                value="{{ $brand->product_type ?? old('product_type') }}" required>
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
            <label for="meta_description">Meta Description</label>
            <input type="text" name="meta_description" id="meta_description" class="form-control"
                value="{{ $brand->meta_description ?? old('meta_description') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_keyword">Meta Keyword</label>
            <input type="text" name="meta_keyword" id="meta_keyword" class="form-control"
                value="{{ $brand->meta_keyword ?? old('meta_keyword') }}">
        </div>

        <div class="col-lg-6 col-md-6 col-12 form-group">
            <label for="image">Icon Image</label>
            <input type="file" name="image" id="image" class="form-control">
            @if (isset($brand) && $brand->image)
                <img src="{{ asset('storage/' . $brand->image) }}" alt="Brand Image" style="width: 100px; height: auto;">
            @endif
        </div>

        <div class="col-lg-6 col-md-6 col-12 form-group">
            <label for="bannerimage">Banner Image</label>
            <input type="file" name="bannerimage" id="bannerimage" class="form-control">
            @if (isset($brand) && $brand->bannerimage)
                <img src="{{ asset('storage/' . $brand->bannerimage) }}" alt="Banner Image"
                    style="width: 100px; height: auto;">
            @endif
        </div>
    </div>
        <div class="text-right mt-2"><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
    </div>
    </div>
@endsection
