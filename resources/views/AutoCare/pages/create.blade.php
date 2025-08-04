@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>{{ isset($page) ? 'Edit Page' : 'Add Page' }}</h5>
    <form action="{{ isset($page) ? route('pages.update', $page->id) : route('pages.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if (isset($page))
            @method('PUT')
        @endif
        <div class="row">
        <!-- Title Field -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $page->title ?? old('title') }}"
                required>
        </div>

        <!-- Slug Field -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ $page->slug ?? old('slug') }}"
                required>
        </div>

        <!-- Tyre Search Form -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="tyre_search_form">Tyre Search Form</label>
            <select name="tyre_search_form" id="tyre_search_form" class="form-control">
                <option value="1" {{ isset($page) && $page->tyre_search_form == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ isset($page) && $page->tyre_search_form == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="exclude_sitemap">Exclude Sitemap</label>
            <select name="exclude_sitemap" id="exclude_sitemap" class="form-control">
                <option value="1" {{ isset($page) && $page->exclude_sitemap == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ isset($page) && $page->exclude_sitemap == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <!-- Content Field -->
        <div class="col-lg-12 col-md-12 col-12 form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="6" class="form-control">{{ $page->content ?? old('content') }}</textarea>
        </div>
        <!-- banner Field -->
        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/banner/content-pages/';
            $fallbackPath   = 'frontend/themes/default/img/banner/content-pages/';
            $imageFile = $page->page_banner_path ?? 'sample-page-image.png';
            $domainImageUrl = asset($imagePath . $imageFile);
            $fallbackImageUrl = asset($fallbackPath . $imageFile);
        @endphp
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="page_banner_path">page Banner</label>
            <input type="file" name="page_banner_path" id="page_banner_path" class="form-control" accept="image/*">
            @if (isset($page->page_banner_path))
                <img src="{{ $domainImageUrl }}"onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $page->page_banner_path }}" style="width:100px;">
            @endif
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="sort">Sort Order</label>
            <input type="number" name="sort" class="form-control" id="sort" value="{{ old('sort', $page->sort ?? 0) }}"
                placeholder="Enter sort order">
        </div>
        
    
        <!-- Status Field -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ isset($page) && $page->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ isset($page) && $page->status == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Meta Title Field -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control"
                value="{{ $page->meta_title ?? old('meta_title') }}">
        </div>

        <!-- Meta Keywords Field -->
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                value="{{ $page->meta_keywords ?? old('meta_keywords') }}">
        </div>

        <!-- Meta Description Field -->
        <div class="col-lg-6 col-md-6 col-12 form-group">
            <label for="meta_description">Meta Description</label>
            <textarea name="meta_description" id="meta_description"
                class="form-control">{{ $page->meta_description ?? old('meta_description') }}</textarea>
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