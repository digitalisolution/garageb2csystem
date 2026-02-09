@extends('samples')

@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
        <div class="bg-light p-2 text-center rounded mb-4 border">
            <h5 class="m-0"><strong>{{ isset($customerGroup) ? 'Edit' : 'Create' }} Customer Group</strong></h5>
        </div>
    <form method="POST" action="{{ isset($customerGroup) ? route('customer-groups.update', $customerGroup) : route('customer-groups.store') }}">
        <div class="row">
        @csrf
        @if(isset($customerGroup)) @method('PUT') @endif

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $customerGroup->name ?? '') }}" required>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label>Discount Type</label>
            <select name="discount_type" class="form-control">
                <option value="">-Select Discount Type-</option>
                <option value="amount" {{ old('discount_type', $customerGroup->discount_type ?? '') == 'amount' ? 'selected' : '' }}>Fixed Amount</option>
                <option value="percentage" {{ old('discount_type', $customerGroup->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
            </select>
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label>Discount Value</label>
            <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $customerGroup->discount_value ?? '') }}">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label>Due Date Option</label>
            <select name="due_date_option" id="due_date_option" class="form-control">
                <option value="">-Select Due Date-</option>
                <option value="first_date" {{ old('due_date_option', $customerGroup->due_date_option ?? '') == 'first_date' ? 'selected' : '' }}>First Date of Month</option>
                <option value="last_date" {{ old('due_date_option', $customerGroup->due_date_option ?? '') == 'last_date' ? 'selected' : '' }}>Last Date of Month</option>
                <option value="30_days_invoice_date" {{ old('due_date_option', $customerGroup->due_date_option ?? '') == '30_days_invoice_date' ? 'selected' : '' }}>30 Days From Invoice Date</option>
                <option value="manual" {{ old('due_date_option', $customerGroup->due_date_option ?? '') == 'manual' ? 'selected' : '' }}>Manual</option>
            </select>

            <input type="date" name="manual_due_date" id="manual_due_date" class="form-control mt-2"
               value="{{ old('manual_due_date', isset($customerGroup) ? optional($customerGroup->manual_due_date)->format('Y-m-d') : '') }}"
                style="display: {{ (old('due_date_option') ?? $customerGroup->due_date_option ?? '') == 'manual' ? 'block' : 'none' }};">
        </div>

        <div class="col-lg-3 col-md-6 col-12 form-group">
            <label class="mb-2">Product Types</label>
            @php
                $types = ['tyre' => 'Tyre', 'service' => 'Service'];
                $selectedTypes = old('product_type', $customerGroup->product_type ?? []);
            @endphp
            <div class="d-flex gap-2 align-items-center">
            @foreach($types as $key => $label)
                <div class="item d-flex gap-2 align-items-center">
                    <label><input type="checkbox" name="product_type[]" value="{{ $key }}"
                        {{ in_array($key, $selectedTypes) ? 'checked' : '' }}>
                    {{ $label }}</label>
                </div>
            @endforeach
            </div>
        </div>

        <div class="col-lg-9 col-md-6 col-12 form-group text-right"><label>&nbsp;</label><button class="btn btn-primary">{{ isset($customerGroup) ? 'Update' : 'Create' }}</button></div>
    </div>
    </form>
</div>
    </div>

<script>
    document.getElementById('due_date_option').addEventListener('change', function () {
        document.getElementById('manual_due_date').style.display = this.value === 'manual' ? 'block' : 'none';
    });
</script>

@endsection