@extends('samples')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h2 class="fw-bold mb-3 mb-md-0">Manual Garage Payouts</h2>

        @php
            $mode = get_option('revolut_business_mode', 'sandbox');
            $isAuthorized = \App\Models\RevolutToken::where('mode', $mode)->exists();
        @endphp

        <div class="text-end">
            @if($isAuthorized)
                <span class="badge bg-success fs-6 py-2 px-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Revolut {{ ucfirst($mode) }} Connected
                </span>
            @else
                <span class="badge bg-danger fs-6 py-2 px-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Revolut Not Connected
                </span>
            @endif
        </div>
    </div>

    @if(!$isAuthorized)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-shield-lock fs-4 me-3"></i>
        <div>
            <strong>Revolut not authorized.</strong> Connect to enable payouts.
            <a href="{{ route('AutoCare.revolut.oauth.redirect') }}" class="btn btn-warning ms-3">
                Re-Authorize {{ ucfirst($mode) }}
            </a>
        </div>
    </div>
    @endif

    {{-- Advanced Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Garage</label>
                    <select name="garage_id" class="form-select">
                        <option value="">All Garages</option>
                        @foreach($garages as $garage)
                            <option value="{{ $garage->id }}" {{ request('garage_id') == $garage->id ? 'selected' : '' }}>
                                {{ $garage->garage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Workshop ID / Garage" value="{{ request('search') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Payout Form (now wraps table) --}}
    <form method="POST" action="{{ route('AutoCare.payouts.bulk') }}" id="bulk-form">
        @csrf
        <div class="d-flex justify-content-end mb-3">
            <button type="submit"
                    id="bulk-button"
                    class="btn btn-primary px-4"
                    disabled>
                Pay Selected (0)
            </button>
        </div>

        {{-- Table --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table id="datable_1" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="select-all" {{ !$isAuthorized || $payouts->isEmpty() ? 'disabled' : '' }}>
                            </th>
                            <th>Workshop</th>
                            <th>Garage</th>
                            <th>Customer Paid</th>
                            <th>Commission</th>
                            <th>Card Fees</th>
                            <th>Garage Payout</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($payouts)
                        @foreach($payouts as $payout)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="payout-checkbox"
                                       name="payout_ids[]"
                                       value="{{ $payout->id }}"
                                       {{ $payout->status !== 'pending' || !$isAuthorized ? 'disabled' : '' }}>
                            </td>
                            <td>#{{ $payout->workshop->id }}</td>
                            <td>{{ $payout->garage->garage_name }}</td>
                            <td class="fw-bold text-success">£{{ number_format($payout->customer_paid_amount, 2) }}</td>
                            <td class="fw-bold text-success">£{{ number_format($payout->platform_commission, 2) }}</td>
                            <td class="fw-bold text-success">£{{ number_format($payout->card_processing_fee, 2) }}</td>
                            <td class="fw-bold text-success">£{{ number_format($payout->payout_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $payout->status == 'completed' ? 'success' : ($payout->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                           <!-- <td>
    @if($payout->invoice_path && Storage::disk('public')->exists($payout->invoice_path))
        <a href="{{ asset('storage/' . $payout->invoice_path) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-file-earmark-pdf"></i> View / Download
        </a>
    @else
        <span class="text-muted">—</span>
    @endif
</td> -->
                            <td class="text-end">
                                @if($payout->status === 'pending' && $isAuthorized)
                                    <button type="submit"
                                            formaction="{{ route('AutoCare.payouts.payout', $payout->id) }}"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Pay £{{ number_format($payout->payout_amount, 2) }} to {{ addslashes($payout->garage->garage_name) }}?')">
                                        Pay Now
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                {{ $payouts->appends(request()->query())->links() }}
            </div>
        </div>
    </form>
</div>

<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.payout-checkbox');
    const bulkButton = document.getElementById('bulk-button');

    @if($isAuthorized)
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(cb => {
                if (!cb.disabled) cb.checked = selectAll.checked;
            });
            updateBulkCount();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkCount));

    function updateBulkCount() {
        const count = document.querySelectorAll('.payout-checkbox:checked').length;
        bulkButton.textContent = `Pay Selected (${count})`;
        bulkButton.disabled = count === 0;
    }

    updateBulkCount();
    @endif
</script>
@endsection