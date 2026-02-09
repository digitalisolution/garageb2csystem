@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
        <h3>{{ isset($tyrePricing) ? 'Edit' : 'Add' }} Tyre Pricing</h3>
        <!-- Display JSON Errors -->
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>There were some errors with your submission:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Display Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
            <form
                action="{{ isset($tyrePricing) ? route('AutoCare.pricing.update', $tyrePricing->pricing_id) : route('AutoCare.pricing.store') }}"
                method="POST">
                @csrf
                @if(isset($tyrePricing))
                    @method('PUT')
                @endif
                <div class="row">
                <!-- General Fields -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pricing_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="pricing_name" name="pricing_name"
                            value="{{ old('pricing_name', $tyrePricing->pricing_name ?? '') }}" required>
                        @error('pricing_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-control" id="supplier_id" name="supplier_id" required>
                        <option value="0" {{ old('supplier_id', $tyrePricing->supplier_id ?? '') == 0 ? 'selected' : ''
                            }}>
                            All
                        </option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $tyrePricing->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                                @if($supplier->garage)
                                    ({{ $supplier->garage->garage_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <label for="order_type_id" class="form-label">Order Type</label>
                    <select class="form-control" id="order_type_id" name="order_type_id" required>
                        <option value="0" {{ old('order_type_id', $tyrePricing->order_type_id ?? 0) == 0 ? 'selected' :
    '' }}>
                            All
                        </option>
                        @foreach ($OrderTypes as $OrderType)
                                            <option value="{{ $OrderType->id }}" {{ old('order_type_id', $tyrePricing->order_type_id ?? '')
        == $OrderType->id ? 'selected' : '' }}>
                                                {{ $OrderType->ordertype_name }}
                                            </option>
                        @endforeach
                    </select>

                    @error('order_type_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <label for="product_type" class="form-label">Product Type</label>
                    <select class="form-control" id="product_type" name="product_type" required>
                        <option value="all" {{ old('product_type', $tyrePricing->product_type ?? '') == 'all' ?
    'selected' : '' }}>
                            All</option>

                        <option value="tyre" {{ old('product_type', $tyrePricing->product_type ?? '') == 'tyre' ?
    'selected' : '' }}>
                            Tyre
                        </option>

                    </select>
                    @error('product_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

                <div class="col-md-4">
                    <div class="form-group">
                    <label for="status" class="form-label">status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1" {{ old('status', $tyrePricing->status ?? '') == '1' ?
    'selected' : '' }}>
                            Active</option>
                
                        <option value="0" {{ old('status', $tyrePricing->status ?? '') == '0' ?
    'selected' : '' }}>
                            inactive
                        </option>
                
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                        value="{{ old('sort_order', $tyrePricing->sort_order ?? '') }}" required>
                    @error('sort_order')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                </div>
            </div>
                <!-- Set active tab based on margin_type -->
                @php
$activeTab = old('margin_type', $tyrePricing->margin_type ?? 'defaultmargin');
                @endphp

                <ul class="nav nav-tabs mt-5" id="pricingTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab == 'defaultmargin' ? 'active' : '' }}"
                            id="simple-markup-tab" data-bs-toggle="tab" data-bs-target="#simple-markup"
                            type="button">Simple Markup</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab == 'prizebysize' ? 'active' : '' }}" id="size-markup-tab"
                            data-bs-toggle="tab" data-bs-target="#size-markup" type="button">Markup by Size</button>
                    </li>
                </ul>
                <input type="hidden" id="margin_type" name="margin_type" value="{{ $activeTab }}">
                <input type="hidden" id="sync_action" name="sync_action" value="0">

                <div class="tab-content mt-3">
                    <div class="tab-pane fade {{ $activeTab == 'defaultmargin' ? 'show active' : '' }}"
                        id="simple-markup">
                        <div class="mb-3">
                            <label for="default_price" class="form-label">Simple Price</label>
                            <input type="number" step="0.01" class="form-control" id="default_price"
                                name="default_price"
                                value="{{ old('default_price', $tyrePricing->default_price ?? '') }}">
                        </div>
                    </div>
                    <div class="tab-pane fade {{ $activeTab == 'prizebysize' ? 'show active' : '' }}" id="size-markup">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    @foreach ($categories as $category)
                                        <th>{{ $category }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>

@php
    $sizesData = $priceData['sizes'] ?? [];
@endphp

@php
    $sizesData = $priceData['sizes'] ?? [];
@endphp

@foreach ($price_by_size as $size)
<tr>
    <td>{{ $size }}</td>

    @foreach ($categories as $categoryKey => $categoryName)
        <td>
            @php
                $value = $sizesData[$size][$categoryKey] ?? null;
            @endphp

            <input type="number"
                   step="0.01"
                   class="form-control"
                   name="price_by_size[sizes][{{ $size }}][{{ $categoryKey }}]"
                   value="{{ old("price_by_size.sizes.$size.$categoryKey", $value) }}">
        </td>
    @endforeach
</tr>
@endforeach

                            </tbody>
                        </table>
                    </div>

                </div>


        <div class="mt-4">
            <button type="submit" class="btn btn-primary" onclick="setSyncAction(0)">Save</button>
            <button type="submit" class="btn btn-success" onclick="setSyncAction(1)">Save and Sync</button>
        </div>
        </form>
    </div>
</div>
</div>


<!-- JavaScript for Filtering Categories -->
<script>
    function filterCategory() {
        const selectedCategory = document.getElementById('category').value;
        document.querySelectorAll('.category').forEach(category => {
            if (selectedCategory === 'all' || category.dataset.category === selectedCategory) {
                category.style.display = 'flex';
            } else {
                category.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        filterCategory();
    });
</script>
<script>
    // Update hidden margin_type input based on selected tab
    document.getElementById('simple-markup-tab').addEventListener('click', () => {
        document.getElementById('margin_type').value = 'defaultmargin';
    });

    document.getElementById('size-markup-tab').addEventListener('click', () => {
        document.getElementById('margin_type').value = 'prizebysize';
    });

    // Set sync_action value based on button clicked
    function setSyncAction(value) {
        document.getElementById('sync_action').value = value;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection