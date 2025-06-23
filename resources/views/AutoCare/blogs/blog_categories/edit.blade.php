@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3">
    <h5>Edit Blog Category</h5>
<form action="{{ route('AutoCare.blogs.blog_categories.update', $category->category_id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
    <div class="col-lg-5 col-md-6 col-12">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ $category->title }}" required>
    </div>

    <div class="col-lg-5 col-md-6 col-12">
        <label>Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ $category->slug }}">
    </div>
    <div class="col-lg-2 col-md-6 col-12">
        <label>&nbsp;</label>
    <button type="submit" class="btn btn-primary btn-block btn-sm">Update</button>
</div>
    </div>
</form>
</div>
</div>
@endsection