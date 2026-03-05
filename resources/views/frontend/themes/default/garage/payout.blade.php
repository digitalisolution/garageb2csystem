@extends('layouts.app')

@section('content')

@section('content')
<div class="pt-60 pb-60">
<div class="container">
    @include('garage.menu')
    <div class="short__item">
    <div class="bg-gray p-3 text-center rounded mb-4">
        <div class="d-flex justify-content-between align-items-center ">
        <h2 class="fw-bold mb-0"> Payout Invoices</h2>
        <a href="{{ route('garage.orders') }}" class="btn btn-outline-secondary">
            ← Back to Orders
        </a>
    </div>
    </div>
    {{-- Status Summary Cards --}}
    <div class="invoice_bank">
        <div class="item">
            Issued
            <span class="bg-red">{{ $statusCounts['issued'] ?? 0 }}</span>
        </div>
        <div class="item">
            Sent
            <span class="bg-green">{{ $statusCounts['sent'] ?? 0 }}</span>
        </div>
        <div class="item">
            Void
            <span class="bg-orange">{{ $statusCounts['void'] ?? 0 }}</span>
        </div>
        <div class="item">
            Total Amount
            <span class="bg-blue">£{{ number_format($invoices->sum(fn($i)=>$i->amount), 2) }}</span>
        </div>
    </div>
    
    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="issued" {{ request('status')=='issued'?'selected':'' }}>Issued</option>
                        <option value="sent" {{ request('status')=='sent'?'selected':'' }}>Sent</option>
                        <option value="void" {{ request('status')=='void'?'selected':'' }}>Void</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Invoice # or Workshop ID" value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-theme w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Invoices Table --}}
        <div class="table-responsive">
            <table class="table table-striped binvoice text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Invoice #</th>
                        <th>Workshop</th>
                        <th>Issue Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <strong>{{ $invoice->invoice_number }}</strong>
                                @if($invoice->revolut_transaction_id)
                                    <br><small class="text-muted" style="font-size:11px;">
                                        Tx: {{ Str::limit($invoice->revolut_transaction_id, 12) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                #{{ $invoice->garagePayout->workshop->id }}
                                <br><small class="text-muted">{{ $invoice->garagePayout->workshop->created_at?->format('d M Y') }}</small>
                            </td>
                            <td>{{ $invoice->issue_date?->format('d M Y') }}</td>
                            <td class="fw-bold">£{{ number_format($invoice->amount, 2) }}</td>
                            <td>
                                @if($invoice->status === 'issued')
                                    <span class="badge bg-secondary">Issued</span>
                                @elseif($invoice->status === 'sent')
                                    <span class="badge bg-success">Sent</span>
                                @elseif($invoice->status === 'void')
                                    <span class="badge bg-danger">Void</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ ucfirst($invoice->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('garage.payoutInvoices.view', $invoice) }}" 
                                       target="_blank"
                                       class="btn btn-info btn-sm text-white"
                                       title="View Invoice">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('garage.payoutInvoices.download', $invoice) }}" 
                                       class="btn btn-dark btn-sm text-white"
                                       title="Download PDF">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No payout invoices found.
                                <br><small>Invoices appear here after successful payouts.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
</div>
</div>
</div>
@endsection
