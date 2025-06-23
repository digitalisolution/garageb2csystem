@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <h5>{{ isset($blog) ? 'Edit blog' : 'Add blog' }}</h5>

    <form action="{{ isset($blog) ? route('blog.update', $blog->blog_id) : route('blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($blog))
            @method('PUT')
        @endif
        <div class="row">
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $blog->title ?? old('title') }}" required>

        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control"
                value="{{ $blog->slug ?? old('slug') }}" required>
        </div>

        <div class="col-lg-12 col-md-12 col-12 form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $blog->description ?? old('description') }}</textarea>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="view">Viewer</label>
            <input type="number" name="view" id="view" class="form-control"
                value="{{ $blog->view ?? old('view') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ $blog->sort_order ?? old('sort_order') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="1" {{ (isset($blog) && $blog->status == 1) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($blog) && $blog->status == 0) ? 'selected' : '' }}>Inactive</option>
            </select>

        </div>
        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="category">Category</label>
            @php
    $oldCategories = old('category_id', $selectedCategories ?? []);
@endphp

<select name="category_id[]" id="category_id" class="form-control" required>
    @foreach ($categories as $category)
        <option value="{{ $category->category_id }}"
            {{ in_array($category->category_id, $oldCategories) ? 'selected' : '' }}>
            {{ $category->title }}
        </option>
    @endforeach
</select>
</div>


        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title" class="form-control"
                value="{{ $blog->meta_title ?? old('meta_title') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_description">Meta Description</label>
            <input type="text" name="meta_description" id="meta_description" class="form-control"
                value="{{ $blog->meta_description ?? old('meta_description') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="meta_keyword">Meta Keyword</label>
            <input type="text" name="meta_keyword" id="meta_keyword" class="form-control"
                value="{{ $blog->meta_keyword ?? old('meta_keyword') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" class="form-control">
            @if (isset($blog) && $blog->image)
                <img src="{{ asset($blog->image) }}" alt="Banner Image" style="width: 100px; height: auto;">

            @endif
        </div>
    </div>
        <div class="text-right mt-2"><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
    </div>
    </div>
@endsection
