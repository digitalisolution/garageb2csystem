@extends('samples')
@section('content')
<div class="container-fluid">
    <div class="bg-white p-3">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Tyre Pricing Management</h3>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('AutoCare.pricing.create') }}" class="btn btn-primary">Add New Pricing</a>
            <button id="sync-pricing-btn" class="btn btn-success">Sync Pricing</button>
        </div>
    </div>
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Supplier</th>
                    <th>Order Type</th>
                    <th>Sort Order</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tyrePricings as $tyrePricing)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $tyrePricing->pricing_name }}</td>
                        <td>{{ $tyrePricing->supplier_id }}</td>
                        <td>{{ $tyrePricing->order_type_id }}</td>
                        <td>{{ $tyrePricing->sort_order }}</td>
                        <td class="text-center">
                            <span class="badge {{ $tyrePricing->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $tyrePricing->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('AutoCare.pricing.edit', ['tyrePricing' => $tyrePricing->pricing_id]) }}"
                                class="btn btn-sm btn-warning">Edit</a>
                            <form
                                action="{{ route('AutoCare.pricing.destroy', ['tyrePricing' => $tyrePricing->pricing_id]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this pricing?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">No tyre pricing records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
<script>
    $('#sync-pricing-btn').click(function () {
        if (confirm('Are you sure you want to sync the pricing?')) {
            $.ajax({
                url: '{{ route('AutoCare.pricing.sync') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sync_action: 1, // Trigger the sync action
                },
                beforeSend: function () {
                    $('#sync-pricing-btn').text('Syncing...').prop('disabled', true);
                },
                success: function (response) {
                    alert('Pricing synced successfully.');
                    console.log(response);
                },
                error: function (xhr) {
                    alert('Failed to sync pricing. Please try again.');
                    console.error(xhr.responseText);
                },
                complete: function () {
                    $('#sync-pricing-btn').text('Sync Pricing').prop('disabled', false);
                }
            });
        }
    });

</script>
@endsection