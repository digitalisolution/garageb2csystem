@extends('samples')
@section('content')
<section class="container-fluid">
    <div class="bg-white p-3">
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
    </div>
    <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Tyre Pricing Management
                        
                        
                        <button id="sync-pricing-btn" class="btn btn-success text-center float-right">Sync Pricing</button>
                        <a href="{{ route('AutoCare.pricing.create') }}" class="btn btn-primary text-center float-right mr-2"><i class="fa fa-plus"></i> Add Pricing</a>
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
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
        </div>

</section>
<script>
    $('#sync-pricing-btn').click(function () {
        if (confirm('Are you sure you want to sync the pricing?')) {
            $.ajax({
                url: '{{ route('AutoCare.pricing.sync') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sync_action: 1,
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