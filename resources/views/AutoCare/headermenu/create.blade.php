@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <h5>{{ isset($page) ? 'Edit Page' : 'Add Page' }}</h5>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form action="{{ isset($page) ? route('headermenu.update', $page->id) : route('headermenu.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($page))
                    @method('PUT')
                @endif
                <div class="row">
                    <!-- Title Field -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ $page->title ?? old('title') }}" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="parent_id">Parent Page</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">None</option>
                            @foreach ($parentPages as $id => $title)
                                <option value="{{ $id }}" {{ isset($page) && $page->parent_id == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Parent Type Dropdown -->
                    <!-- Parent Type Dropdown -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="parent_type">Parent Type</label>
                        <select name="parent_type" id="parent_type" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="services" {{ old('parent_type', $page->parent_type ?? '') == 'services' ? 'selected' : '' }}>Services</option>
                            <option value="pages" {{ old('parent_type', $page->parent_type ?? '') == 'pages' ? 'selected' : '' }}>Pages</option>
                            <option value="brands" {{ old('parent_type', $page->parent_type ?? '') == 'brands' ? 'selected' : '' }}>Brands</option>
                        </select>
                    </div>

                    <!-- Slug Dropdown -->
                    <!-- Slug Dropdown -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="related_slug">Select Slug</label>
                        <select name="slug" id="related_slug" class="form-control">
                            <option value="#">-- Select slug --</option>
                            @if (!empty($page->slug))
                                <option value="{{ $page->slug }}" selected>{{ $page->slug }}</option>
                            @endif
                        </select>
                    </div>


                    <!-- Status Field -->
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ isset($page) && $page->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ isset($page) && $page->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="sort">Sort Order</label>
                        <input type="number" name="sort" class="form-control" id="sort"
                            value="{{ old('sort', $page->sort ?? 0) }}" placeholder="Enter sort order">
                    </div>
                </div>
                <div class="text-right mt-2"><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const parentTypeSelect = document.getElementById('parent_type');
    const relatedSlugSelect = document.getElementById('related_slug');

    parentTypeSelect.addEventListener('change', function () {
        const selectedType = this.value;
        relatedSlugSelect.innerHTML = '<option value="">-- Select slug --</option>';
        relatedSlugSelect.disabled = true;

        if (!selectedType) return;

        fetch(`{{ route("get.slugs", ":type") }}`.replace(":type", selectedType))
            .then(response => response.json())
            .then(data => {
                relatedSlugSelect.disabled = false;
                Object.entries(data).forEach(([key, slug]) => {
                    const option = document.createElement('option');
                    option.value = slug;
                    option.textContent = slug.replace('brand/', '').replace(/-/g, ' ');
                    relatedSlugSelect.appendChild(option);
                });
            })
            .catch(() => {
                alert('Failed to load slugs.');
            });
    });
});

</script>