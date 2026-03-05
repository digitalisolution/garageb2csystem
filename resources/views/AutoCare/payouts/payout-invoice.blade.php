<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.3;
            color: #000;
            background: #fff;
            font-size: 15px;
        }

        /* Layout */
        .invoice-container {
            width: 800px;
            margin: 50px auto;
            padding: 50px;
            border: solid 1px #ccc;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info p {
            margin: 3px 0;
            font-size: 15px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .invoice-meta p {
            margin: 4px 0;
            font-size: 15px;
        }

        .invoice-meta .invoice-number {
            font-weight: bold;
            color: #27ae60;
            font-size: 14px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .status-issued {
            background: #e8f4fd;
            color: #2980b9;
        }

        .status-sent {
            background: #e8f8f0;
            color: #27ae60;
        }

        .status-void {
            background: #fdedec;
            color: #c0392b;
            text-decoration: line-through;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 600;
        }

        /* Totals */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 7px;
            font-size: 15px;
        }

        .totals-table .total {
            font-weight: bold;
            font-size: 15px;
        }

        /* Notes & Footer */
        .notes-section h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .notes-section p {
            font-size: 12px;
            color: #666;
            margin: 3px 0;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Action Buttons (Screen Only) */
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .action-buttons .btn {
            display: inline-block;
            padding: 10px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #27ae60;
            color: white;
        }

        .btn-primary:hover {
            background: #219a52;
        }

        .btn-secondary {
            background: #3498db;
            color: white;
        }

        .btn-secondary:hover {
            background: #2980b9;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #2c3e50;
            color: #2c3e50;
        }

        .btn-outline:hover {
            background: #2c3e50;
            color: white;
        }

        .table-suppay {
            width: 100%;
            margin-top: 30px;
        }

        .table-suppay thead tr th,
        .table-suppay tbody tr td {
            padding: 7px;
            border: solid 1px #ccc;
        }

        .table-suppay thead {
            background: #eee;
        }

        .address-area {
            margin-bottom: 20px;
        }

        .bottom-details {
            margin: 40px 0;
        }
    </style>
</head>

<body>

    {{-- Void Stamp if applicable --}}
    @if($invoice->status === 'void')
        <div class="void-stamp">VOID</div>
    @endif

    <div class="invoice-container">
        @php
            $domain = request()->getHost();
            $domain = str_replace('.', '-', $domain);

            $logoPath = "frontend/{$domain}/img/garage_logo/{$payout->garage->garage_logo}";
            $themeLogo = "frontend/themes/{$garage->theme}/img/garage_logo/{$payout->garage->garage_logo}";
            $defaultLogo = "frontend/themes/default/img/logo/logo.png";

            // Check which file exists
            if (file_exists(public_path($logoPath))) {
                $src = $logoPath;
            } elseif (file_exists(public_path($themeLogo))) {
                $src = $themeLogo;
            } else {
                $src = $defaultLogo;
            }
            $isPdf = $isPdf ?? false;

            if ($isPdf) {
                $logoFullPath = public_path($src);
                if (file_exists($logoFullPath)) {
                    $logoSrc = 'data:image/' . pathinfo($logoFullPath, PATHINFO_EXTENSION) . ';base64,' .
                        base64_encode(file_get_contents($logoFullPath));
                } else {
                    $logoSrc = $defaultLogo;
                }
            } else {
                $logoSrc = asset($src) . '?v=' . filemtime(public_path($src));
            }
        @endphp

        {{-- Header with Logo --}}
        <div class="invoice-header">
            <h1 style="margin-bottom:15px;">PAYOUT INVOICE</h1>
            <img src="{{ $logoSrc }}" alt="{{ $garage->garage_name }} Logo" loading="lazy" height="40"
                style="max-width: 200px; object-fit: contain;">
        </div>
        <div class="invoice-header">
            <div class="company-info">
                <p class="invoice-number"><strong>Invoice Number:</strong> #{{ $invoice->invoice_number }}</p>
                <p><strong>Issued Date:</strong> {{ $issueDate }}</p>
                @if($invoice->revolut_transaction_id)
                    <p><strong>Revolut Tx:</strong><br><small>{{ $invoice->revolut_transaction_id }}</small></p>
                @endif
                <span class="status-badge status-{{ $invoice->status }}">
                    <strong>{{ ucfirst($invoice->status) }}</strong>
                </span>
            </div>
            <div class="invoice-meta">
                <p><strong>Job Date:</strong> {{ $workshop->created_at?->format('d F Y, h:i A') ?? 'N/A' }}</p>
                <p><strong>Payment Status:</strong> <span
                        style="color: {{ $payout->status === 'completed' ? '#27ae60' : '#e74c3c' }}; font-weight: 600;">
                        {{ ucfirst($payout->status) }}</span></p>
            </div>
        </div>

        <table cellpadding="0" cellspacing="0" border="0" class="table-suppay">
            <thead>
                <tr>
                    <th align="left">Payee (Issuer of Invoice)</th>
                    <th align="left">Supplier (Partner Garage)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="50%">
                        <div class="address-area">
                            <p>{{$garage->garage_name}}<br>
                                {{ $garage->street }}<br>
                                {{ $garage->city }}, {{ $garage->zone }}<br>
                                United Kingdom</p>
                        </div>
                        <p>VAT No: {{ $garage->vat_number }}</p>
                    </td>
                    <td width="50%">
                        <div class="address-area">
                            <p>{{$payout->garage->garage_name}}<br>
                                {{ $payout->garage->garage_street }}<br>
                                {{ $payout->garage->garage_city }}, {{ $payout->garage->garage_zone }}<br>
                                United Kingdom</p>
                        </div>
                        @if($payout->garage->garage_vat_number)
                        <p>VAT No: {{ $payout->garage->garage_vat_number }}</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
@php
    // Prepare data
    $tyresCount = $payout->tyres_count ?? $workshop->items?->count() ?? 0;
    $services = $workshop->services ?? collect();
    $hasTyres = $tyresCount > 0;
    $hasServices = $services->isNotEmpty();
    
    $isVatRegistered = isset($payout->garage->garage_fitting_vat_class)
        && $payout->garage->garage_fitting_vat_class == 9;
    $vatRate = 0.20;

    // Track totals
    $netTotal = 0;
    $vatTotal = 0;
    $processingFee = $payout->card_processing_fee ?? 0;

    // Format helper: returns [net, vat, gross]
    $calculateVat = function ($amount, $taxClassId) use ($isVatRegistered, $vatRate) {
        if ($isVatRegistered && $taxClassId == 9 && $amount > 0) {
            $net = $amount / (1 + $vatRate);
            $vat = $amount - $net;
            return ['net' => $net, 'vat' => $vat, 'gross' => $amount];
        }
        return ['net' => $amount, 'vat' => 0, 'gross' => $amount];
    };

    // --- TYRES CALCULATION ---
    $tyreItems = [];
    $totalTyreNet = 0;
    $totalTyreVat = 0;

    if ($hasTyres) {
        foreach ($workshop->items as $tyre) {
            $fittingPrice = $tyre->garage_fitting_charges ?? 0;
            $itemqty = $tyre->quantity ?? 1;
            $commissionRate = $payout->garage->commission_price ?? 0;

            // Calculate garage payout (commission logic)
            if ($garage->commission_type === 'Percentage') {
                $commissionAmount = $fittingPrice * ($commissionRate / 100);
                $garagePayout = $fittingPrice - $commissionAmount;
            } else {
                $garagePayout = $fittingPrice - ($commissionRate * $itemqty);
            }

            // Get tax class from tyre or fallback to garage default
            $taxClassId = $tyre->tax_class_id ?? $payout->garage->garage_fitting_vat_class ?? null;
            $vatData = $calculateVat($garagePayout, $taxClassId);

            $tyreItems[] = [
                'job_id' => $workshop->id,
                'description' => 'Tyre Fitting Commission',
                'quantity' => $itemqty,
                'gross' => $garagePayout,
                'net' => $vatData['net'],
                'vat' => $vatData['vat'],
                'tax_class_id' => $taxClassId,
                'unit_net' => $vatData['net'] / $itemqty,
                'unit_vat' => $vatData['vat'] / $itemqty,
            ];

            $totalTyreNet += $vatData['net'];
            $totalTyreVat += $vatData['vat'];
        }
    }

    // --- SERVICES CALCULATION ---
    $serviceItems = [];
    $totalServiceNet = 0;
    $totalServiceVat = 0;

    if ($hasServices) {
        foreach ($services as $ws) {
            if ($ws->is_void) continue;

            $serviceModel = $ws->service;
            if (!$serviceModel) {
                \Log::warning('Service relation missing', ['service_item_id' => $ws->id]);
                continue;
            }

            $serviceName = e($ws->service_name ?? 'Service');
            $quantity = $ws->service_quantity ?? 1;
            $costPrice = $serviceModel->cost_price ?? 0;
            $commissionPrice = $ws->service_commission_price ?? 0;

            // Calculate commission amount
            $lineGross = 0;
            if ($commissionPrice) {
                $lineGross = ($costPrice - $commissionPrice) * $quantity;
            } else {
                $lineGross = ($ws->service_price ?? 0) * $quantity;
            }

            // Get tax class
            $taxClassId = $ws->tax_class_id ?? null;
            $vatData = $calculateVat($lineGross, $taxClassId);

            $serviceItems[] = [
                'job_id' => $workshop->id,
                'name' => $serviceName,
                'product_type' => $serviceModel->product_type ?? null,
                'quantity' => $quantity,
                'gross' => $lineGross,
                'net' => $vatData['net'],
                'vat' => $vatData['vat'],
                'tax_class_id' => $taxClassId,
                'unit_net' => $vatData['net'] / $quantity,
                'unit_vat' => $vatData['vat'] / $quantity,
            ];

            $totalServiceNet += $vatData['net'];
            $totalServiceVat += $vatData['vat'];
        }
    }

    // --- FINAL TOTALS ---
    $subtotalNet = $totalTyreNet + $totalServiceNet;
    $subtotalVat = $totalTyreVat + $totalServiceVat;
    $grandTotal = $subtotalNet + $subtotalVat - $processingFee;
@endphp
        {{-- Line Items --}}
        <table cellpadding="0" cellspacing="0" border="0" class="table-suppay">
            <thead>
                <tr>
                    <th align="left">Job Ref</th>
                    <th align="left">Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
           <tbody>
    {{-- TYRE ITEMS --}}
    @foreach($tyreItems as $item)
    <tr>
        <td>Job-{{ $item['job_id'] }}</td>
        <td>
            <strong>{{ $item['description'] }}</strong><br>
            <small style="color: #666;">
                {{ $item['quantity'] }} Tyre(s) Fitted • Job #{{ $item['job_id'] }}<br>
                Processed on {{ $workshop->created_at?->format('d M Y') }}
                @if($item['tax_class_id'] == 9)
                    <br><span style="color:#2980b9;font-size:10px;">(VAT 20%)</span>
                @endif
            </small>
        </td>
        <td class="text-center">{{ $item['quantity'] }}</td>
        <td class="text-right">£{{ number_format($item['unit_net'], 2) }}</td>
        <td class="text-right">
            <div style="display:flex;flex-direction:column;align-items:flex-end;">
                <span class="font-bold">£{{ number_format($item['net'], 2) }}</span>
                @if($item['vat'] > 0)
                    <small style="color:#27ae60;">+VAT £{{ number_format($item['vat'], 2) }}</small>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

    {{-- SERVICE ITEMS --}}
    @foreach($serviceItems as $item)
    <tr>
        <td>Job-{{ $item['job_id'] }}</td>
        <td>
            <strong>{{ $item['name'] }}</strong>
            <small style="color: #666;">
                @if($item['product_type'])
                    <span style="background:#e8f4fd;color:#2980b9;padding:2px 6px;border-radius:3px;font-size:10px;">
                        {{ e($item['product_type']) }}
                    </span>
                @endif
                @if($item['tax_class_id'] == 9)
                    <br><span style="color:#2980b9;font-size:10px;">(VAT 20%)</span>
                @endif
            </small>
        </td>
        <td class="text-center">{{ $item['quantity'] }}</td>
        <td class="text-right">£{{ number_format($item['unit_net'], 2) }}</td>
        <td class="text-right">
            <div style="display:flex;flex-direction:column;align-items:flex-end;">
                <span class="font-bold">£{{ number_format($item['net'], 2) }}</span>
                @if($item['vat'] > 0)
                    <small style="color:#27ae60;">+VAT £{{ number_format($item['vat'], 2) }}</small>
                @endif
            </div>
        </td>
    </tr>
    @endforeach

    {{-- FALLBACK: No items --}}
    @if(!$hasTyres && !$hasServices)
        @php
            $fallbackVat = $calculateVat($payout->payout_amount, $payout->garage->garage_fitting_vat_class ?? null);
        @endphp
        <tr>
            <td class="text-center">1</td>
            <td>
                <strong>Garage Settlement Payout</strong><br>
                <small style="color: #666;">
                    Commission settlement for Job #{{ $workshop->id }}<br>
                    Processed on {{ $workshop->created_at?->format('d M Y') }}
                    @if($payout->garage->garage_fitting_vat_class == 9)
                        <br><span style="color:#2980b9;font-size:10px;">(VAT 20%)</span>
                    @endif
                </small>
            </td>
            <td class="text-right">£{{ number_format($fallbackVat['net'], 2) }}</td>
            <td class="text-right">
                <div style="display:flex;flex-direction:column;align-items:flex-end;">
                    <span class="font-bold">£{{ number_format($fallbackVat['net'], 2) }}</span>
                    @if($fallbackVat['vat'] > 0)
                        <small style="color:#27ae60;">+VAT £{{ number_format($fallbackVat['vat'], 2) }}</small>
                    @endif
                </div>
            </td>
        </tr>
    @endif

    {{-- METADATA BREAKDOWN ITEMS --}}
    @if($invoice->metadata && isset($invoice->metadata['breakdown']))
        @foreach($invoice->metadata['breakdown'] as $metaItem)
            @php
                $itemGross = $metaItem['amount'] ?? $metaItem['unit_price'] ?? 0;
                $itemQty = $metaItem['qty'] ?? 1;
                $itemTaxClass = $metaItem['tax_class_id'] ?? null;
                $itemVat = $calculateVat($itemGross, $itemTaxClass);
            @endphp
            <tr>
                <td class="text-center"><small>{{ $itemQty }}</small></td>
                <td><small style="color: #888;">↳ {{ e($metaItem['description'] ?? 'Additional item') }}</small></td>
                <td class="text-right"><small>£{{ number_format($itemVat['net'], 2) }}</small></td>
                <td class="text-right">
                    <small>
                        £{{ number_format($itemVat['net'], 2) }}
                        @if($itemVat['vat'] > 0)
                            <br><span style="color:#27ae60;">+VAT £{{ number_format($itemVat['vat'], 2) }}</span>
                        @endif
                    </small>
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
        </table>
       <div class="totals-wrapper">
    <table class="totals-table">
        {{-- Subtotal (Net) --}}
        <tr>
            <td class="label" width="70%">Subtotal (Net):</td>
            <td class="text-right">£{{ number_format($subtotalNet, 2) }}</td>
        </tr>

        {{-- VAT Line (only if any VAT applies) --}}
        @if($subtotalVat > 0)
        <tr>
            <td class="label">VAT (20% - Reverse Charge):</td>
            <td class="text-right" style="color:#27ae60;">£{{ number_format($subtotalVat, 2) }}</td>
        </tr>
        @endif

        {{-- Processing Fee --}}
        @if($processingFee > 0)
        <tr>
            <td class="label">Processing Fees:</td>
            <td class="text-right">-£{{ number_format($processingFee, 2) }}</td>
        </tr>
        @endif

        {{-- Grand Total --}}
        <tr class="total" style="border-top: 2px solid #2c3e50;">
            <td><strong>TOTAL PAID:</strong></td>
            <td class="text-right"><strong>£{{ number_format($grandTotal, 2) }}</strong></td>
        </tr>

        {{-- VAT Summary Note --}}
        @if($subtotalVat > 0 && $isVatRegistered)
        <tr>
            <td colspan="2" style="font-size: 11px; color: #666; padding-top: 10px; text-align: right;">
                <em>VAT calculated per item based on tax_class_id</em>
            </td>
        </tr>
        @endif
    </table>
</div>

        @if($invoice->notes || $payout->notes)
            <div class="notes-section">
                <h4>📝 Notes</h4>
                @if($invoice->notes)
                    <p>{!! nl2br(e($invoice->notes)) !!}</p>
                @endif
                @if($payout->notes && $payout->notes !== $invoice->notes)
                    <p>{!! nl2br(e($payout->notes)) !!}</p>
                @endif
            </div>
        @endif
        <div class="bottom-details">
            <p><strong>Payment Terms:</strong> 7 Days from Invoice Date</p>
            <p><strong>Bank Name:</strong>{{ $payout->garage->garage_bank_name }}</p>
            <p><strong>Account Number:</strong> {{ $payout->garage->garage_account_number }}</p>
            <p><strong>Sort Code:</strong> {{ $payout->garage->garage_bank_sort_code }}</p><br>
            @if(isset($payout->garage->garage_fitting_vat_class) && $payout->garage->garage_fitting_vat_class == 9)
                <p>This invoice is issued in accordance with UK VAT regulations. VAT charged at the standard rate of 20%.
                </p><br>
            @endif
            <p><strong>Authorised By: Accounts Department, TYRE LAB LTD</strong></p>
        </div>

        <div class="action-buttons no-print">
            <a href="{{ route('garage-payout-invoices.download', $invoice) }}" class="btn btn-primary">
                💾 Download PDF
            </a>
            @if($garage->garage_email && auth()->user()?->isAdmin())
                <form method="POST" action="{{ route('garage-payout-invoices.send', $invoice) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary"
                        onclick="return confirm('Send invoice to {{ addslashes($garage->garage_email) }}?')">
                        📧 Email Invoice
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="btn btn-outline">
                🖨️ Print Invoice
            </button>
        </div>
    </div>

</body>

</html>