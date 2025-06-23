@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3">
    <h5>Create Blog Category</h5>
<form action="{{ route('AutoCare.blogs.blog_categories.store') }}" method="POST">
    @csrf
    <div class="row">
    <div class="col-lg-5 col-md-6 col-12">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="col-lg-5 col-md-6 col-12">
        <label>Slug</label>
        <input type="text" name="slug" class="form-control" placeholder="Leave blank to auto-generate">
    </div>
    <div class="col-lg-2 col-md-6 col-12">
        <label>&nbsp;</label>
    <button type="submit" class="btn btn-primary btn-sm btn-block">Create</button>
</div>
    </div>
</form>
</div>
</div>
@endsection
