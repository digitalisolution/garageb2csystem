@extends('samples')

@section('content')
<div class="container-fluid">
<div class="bg-white p-3 mb-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <h5>Manual Garage Payouts</h5>
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
        <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label>Garage</label>
                    <select name="garage_id" class="form-control">
                        <option value="">All Garages</option>
                        @foreach($garages as $garage)
                            <option value="{{ $garage->id }}" {{ request('garage_id') == $garage->id ? 'selected' : '' }}>
                                {{ $garage->garage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Workshop ID / Garage" value="{{ request('search') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
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
        <div class="card">
            <div class="card-body table-responsive" style="font-size: 13px;padding-left:10px;vertical-align:middle;min-height:350px;">
                <table id="datable_1" class="table table-hover table-sm align-middle">
                    <thead class="thead-dark">
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
    @if($payout->payoutInvoice_path && Storage::disk('public')->exists($payout->payoutInvoice_path))
        <a href="{{ asset('storage/' . $payout->payoutInvoice_path) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-file-earmark-pdf"></i> View / Download
        </a>
    @else
        <span class="text-muted">—</span>
    @endif
</td> -->
                            <!-- <td class="text-end">
                                @if($payout->status === 'pending' && $isAuthorized)
                                    <button type="submit"
                                            formaction="{{ route('AutoCare.payouts.payout', $payout->id) }}"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Pay £{{ number_format($payout->payout_amount, 2) }} to {{ addslashes($payout->garage->garage_name) }}?')">
                                        Pay Now
                                    </button>
                                @endif
                            </td> -->

                            <td class="text-end">
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                type="button" 
                id="actionDropdown{{ $payout->id }}" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                {{ !$isAuthorized ? 'disabled' : '' }}>
            Actions
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown{{ $payout->id }}">
            
            @if($payout->status === 'pending')
                <li>
                                @if($payout->status === 'pending' && $isAuthorized)
                                    <button type="submit"
                                            formaction="{{ route('AutoCare.payouts.payout', $payout->id) }}"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Pay £{{ number_format($payout->payout_amount, 2) }} to {{ addslashes($payout->garage->garage_name) }}?')">
                                        Pay Now
                                    </button>
                                @endif
                </li>
            @endif
            @if($payout->status === 'completed')
                @if($payout->payoutInvoice)
                    {{-- View Invoice --}}
                    <li>
                        <a href="{{ route('garage-payout-invoices.view', $payout->payoutInvoice) }}" 
                           target="_blank" 
                           class="dropdown-item">
                            <i class="bi bi-eye me-2"></i> View Invoice
                        </a>
                    </li>
                    
                    {{-- Download Invoice --}}
                    <li>
                        <a href="{{ route('garage-payout-invoices.download', $payout->payoutInvoice) }}" 
                           class="dropdown-item">
                            <i class="bi bi-download me-2"></i> Download Invoice
                        </a>
                    </li>
                    
                    {{-- Send Invoice --}}
@if($payout->garage->garage_email)
    <li>
        <a href="#" 
           class="dropdown-item send-invoice-btn"
           data-invoice-id="{{ $payout->payoutInvoice->id }}"
           data-invoice-number="{{ $payout->payoutInvoice->invoice_number }}"
           data-garage-email="{{ addslashes($payout->garage->garage_email) }}"
           data-csrf="{{ csrf_token() }}"
           onclick="return false;">
            <i class="bi bi-envelope me-2"></i> Send Invoice
        </a>
    </li>
@endif
                    
                    <li><hr class="dropdown-divider"></li>
                @else
                    <li>
                        <span class="dropdown-item text-muted">
                            <i class="bi bi-info-circle me-2"></i> No invoice generated
                        </span>
                    </li>
                @endif
            @endif

            {{-- PROCESSING: Show Waiting --}}
            @if($payout->status === 'processing')
                <li>
                    <span class="dropdown-item text-warning">
                        <i class="bi bi-hourglass-split me-2"></i> Processing...
                    </span>
                </li>
            @endif

            {{-- FAILED: Show Retry --}}
            @if($payout->status === 'failed')
                <li>
                    <form method="POST" action="{{ route('AutoCare.payouts.payout', $payout->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="dropdown-item text-danger"
                                onclick="return confirm('Retry payout for £{{ number_format($payout->payout_amount, 2) }}?')">
                            <i class="bi bi-arrow-repeat me-2"></i> Retry Payout
                        </button>
                    </form>
                </li>
            @endif
        </ul>
    </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle "Send Invoice" clicks
    document.querySelectorAll('.send-invoice-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const invoiceId = this.dataset.invoiceId;
            const invoiceNumber = this.dataset.invoiceNumber;
            const garageEmail = this.dataset.garageEmail;
            const csrfToken = this.dataset.csrf;
            
            // Confirm before sending
            if (!confirm(`Send invoice #${invoiceNumber} to ${garageEmail}?`)) {
                return;
            }
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Sending...';
            this.classList.add('disabled');
            
            // Submit via fetch to correct endpoint
            fetch(`/AutoCare/garage-payout-invoices/${invoiceId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                // Handle success
                if (data.success) {
                    alert('✅ Invoice sent successfully!');
                    // Optional: Update UI to show "Sent" badge
                    location.reload(); // Refresh to show updated status
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to send invoice'));
                    this.innerHTML = originalText;
                    this.classList.remove('disabled');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Network error. Please try again.');
                this.innerHTML = originalText;
                this.classList.remove('disabled');
            });
        });
    });
    
});
</script>
@endsection
