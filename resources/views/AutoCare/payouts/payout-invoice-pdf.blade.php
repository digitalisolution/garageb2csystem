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
            font-size: 13px;
        }

        /* Layout */
        .invoice-container {
            width: 600px;
            margin: 30px auto;
            padding: 30px;
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
            font-size: 13px;
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
            font-size: 13px;
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
            font-size: 13px;
        }

        .totals-table .total {
            font-weight: bold;
            font-size: 13px;
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
            margin-top: 10px;
        }

        .action-buttons .btn {
            display: inline-block;
            padding: 10px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
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
            margin-top: 20px;
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
            margin: 20px 0;
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
            <table width="100%">
                <tr>
                    <td><h1 style="margin-bottom:15px;">PAYOUT VAT INVOICE</h1></td>
                    <td align="right"><img src="{{ $logoSrc }}" alt="{{ $garage->garage_name }} Logo" loading="lazy" height="30" style="max-width: 200px; object-fit: contain;"></td>
                </tr>
            </table>
        </div>
        <div class="invoice-header">
            <table width="100%">
                <tr>
                    <td valign="top">
                        <div class="company-info">
                            <p class="invoice-number0"><strong>Invoice Number:</strong> #{{ $invoice->invoice_number }}</p>
                            <p><strong>Issued Date:</strong> {{ $date }}</p>
                            @if($invoice->revolut_transaction_id)
                                <p><strong>Revolut Tx:</strong><br><small>{{ $invoice->revolut_transaction_id }}</small></p>
                            @endif
                            <span class="status-badge status-{{ $invoice->status }}">
                                <strong>{{ ucfirst($invoice->status) }}</strong>
                            </span>
                        </div>
                    </td>
                    <td valign="top">
                        <div class="invoice-meta">
                            <p><strong>Job Date:</strong> {{ $workshop->created_at?->format('d F Y, h:i A') ?? 'N/A' }}</p>
                            <p><strong>Payment Status:</strong> <span
                                    style="color: {{ $payout->status === 'completed' ? '#27ae60' : '#e74c3c' }}; font-weight: 600;">
                                    {{ ucfirst($payout->status) }}</span></p>
                        </div>
                    </td>
                </tr>
            </table>
            
            
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
                @php
                    // Prepare data
                    $tyresCount = $payout->tyres_count ?? $workshop->items?->count() ?? 0;
                    $services = $workshop->services ?? collect();
                    $hasTyres = $tyresCount > 0;
                    $hasServices = $services->isNotEmpty();
                    $totalPayout = $payout->payout_amount;

                    $isVatRegistered = isset($payout->garage->garage_fitting_vat_class)
                        && $payout->garage->garage_fitting_vat_class == 9;
                    $vatRate = 0.20;

                    $totalServiceCommission = $services
                        ->reject(fn($ws) => $ws->is_void ?? false)
                        ->sum(fn($ws) => ($ws->service?->service_commission_price ?? 0) * ($ws->service_quantity ?? 1));
                    $totalTyreCommission = 0;
                    $totalServiceCommission = 0;

                    foreach ($workshop->items as $tyre) {

                        $fittingPrice = $tyre->garage_fitting_charges ?? 0;
                        $itemqty = $tyre->quantity ?? 0;
                        $commissionRate = $payout->garage->commission_price * $itemqty ?? 0;

                        if ($garage->commission_type === 'Percentage') {

                            $commissionAmount = $fittingPrice * ($commissionRate / 100);
                            $garagePayout = $fittingPrice - $commissionAmount;

                        } else {

                            $garagePayout = $fittingPrice - $commissionRate;
                        }
                        $totalTyreCommission += $garagePayout;
                    }

                    $formatPrice = function ($amount) use ($isVatRegistered, $vatRate) {
                        if ($isVatRegistered && $amount > 0) {
                            return $amount / (1 + $vatRate);
                        }
                        return $amount;
                    };
                @endphp

                @if($hasTyres)
                    @php
                        $displayTyreCommission = $formatPrice($totalTyreCommission);
                        $displayTyreUnitPrice = $hasTyres && $itemqty > 0 ? ($displayTyreCommission / $itemqty) : 0;
                    @endphp
                    <tr>
                        <td>Job-{{ $workshop->id }}</td>
                        <td>
                            <strong>Tyre Fitting Commission</strong><br>
                            <small style="color: #666;">
                                {{ $itemqty }} Tyre(s) Fitted • Job #{{ $workshop->id }}<br>
                                Processed on {{ $workshop->created_at?->format('d M Y') }}
                                @if($isVatRegistered)
                                    <br><span style="color:#2980b9;font-size:10px;">(Ex-VAT)</span>
                                @endif
                            </small>
                        </td>
                        <td class="text-center">{{ $itemqty }}</td>
                        <td class="text-right">£{{ number_format($displayTyreUnitPrice, 2) }}</td>
                        <td class="text-right font-bold">£{{ number_format($displayTyreCommission, 2) }}</td>
                    </tr>
                @endif

                @if($hasServices)
                    @foreach($services as $ws)
                        @if($ws->is_void) @continue @endif
                        @php
                            $serviceName = e($ws->service?->service_name ?? $ws->service_name ?? 'Service');
                            $quantity = $ws->service_quantity ?? 1;
                            $grossPrice = $ws->service?->service_commission_price ?? $ws->service_price ?? 0;

                            $displayUnitPrice = $formatPrice($grossPrice);
                            $displayLineTotal = $displayUnitPrice * $quantity;

                            $taxClass = $ws->service?->tax_class_id ?? $ws->tax_class_id ?? null;
                            $showVatBadge = $isVatRegistered && $taxClass == 9;
                        @endphp
                        <tr>
                             <td>Job-{{ $workshop->id }}</td>
                            <td>
                                <strong>{{ $serviceName }}</strong>
                                <small style="color: #666;">
                                    @if($ws->service?->product_type || $ws->product_type)
                                        <span
                                            style="background:#e8f4fd;color:#2980b9;padding:2px 6px;border-radius:3px;font-size:10px;">
                                            {{ e($ws->service?->product_type ?? $ws->product_type) }}
                                        </span>
                                    @endif
                                    @if($showVatBadge)
                                        <br><span style="color:#2980b9;font-size:10px;">(Ex-VAT)</span>
                                    @endif
                                </small>
                            </td>
                            <td class="text-center">{{ $quantity }}</td>
                            <td class="text-right">£{{ number_format($displayUnitPrice, 2) }}</td>
                            <td class="text-right font-bold">£{{ number_format($displayLineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                @if(!$hasTyres && !$hasServices)
                    @php
                        $displayAmount = $formatPrice($totalPayout);
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <strong>Garage Settlement Payout</strong><br>
                            <small style="color: #666;">
                                Commission settlement for Job #{{ $workshop->id }}<br>
                                Processed on {{ $workshop->created_at?->format('d M Y') }}
                                @if($isVatRegistered)
                                    <br><span style="color:#2980b9;font-size:10px;">(Ex-VAT)</span>
                                @endif
                            </small>
                        </td>
                        <td>#{{ $workshop->id }}</td>
                        <td class="text-right">£{{ number_format($displayAmount, 2) }}</td>
                        <td class="text-right font-bold">£{{ number_format($displayAmount, 2) }}</td>
                    </tr>
                @endif

                @if($invoice->metadata && isset($invoice->metadata['breakdown']))
                    @foreach($invoice->metadata['breakdown'] as $item)
                        @php
                            $itemGross = $item['amount'] ?? $item['unit_price'] ?? 0;
                            $itemDisplay = $formatPrice($itemGross);
                        @endphp
                        <tr>
                            <td class="text-center"><small>{{ $item['qty'] ?? 1 }}</small></td>
                            <td><small style="color: #888;">↳ {{ e($item['description'] ?? 'Additional item') }}</small></td>
                            <td><small>-</small></td>
                            <td class="text-right"><small>£{{ number_format($itemDisplay, 2) }}</small></td>
                            <td class="text-right"><small>£{{ number_format($itemDisplay, 2) }}</small></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="totals-wrapper">
            <table class="totals-table">
                @if(isset($payout->garage->garage_fitting_vat_class) && $payout->garage->garage_fitting_vat_class == 9)
                    @php
                        $grossAmount = $displayLineTotal + $displayTyreCommission;
                        $processingFee = $payout->card_processing_fee ?? 0;
                        $amount = ($processingFee + $payout->payout_amount);
                        $vatAmount = $amount - ($amount / 1.2);
                        $finalTotal = ($grossAmount + $vatAmount) - $processingFee;
                    @endphp

                    <tr>
                        <td class="label" width="70%">Subtotal (Net):</td>
                        <td class="text-right">£{{ number_format($grossAmount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">VAT (20% - Reverse Charge):</td>
                        <td class="text-right">£{{ number_format($vatAmount, 2) }}</td>
                    </tr>
                    @if($processingFee > 0)
                        <tr>
                            <td class="label">Processing Fees:</td>
                            <td class="text-right">-£{{ number_format($processingFee, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td><strong>TOTAL PAID:</strong></td>
                        <td class="text-right"><strong>£{{ number_format($finalTotal, 2) }}</strong></td>
                    </tr>

                @else
                    @php
                        $grossAmount = $displayLineTotal + $displayTyreCommission;
                        $processingFee = $payout->card_processing_fee ?? 0;
                        $amount = ($processingFee + $payout->payout_amount);
                        $finalTotal = $grossAmount - $processingFee;
                    @endphp

                    <tr>
                        <td class="label" width="70%">Payout Amount</td>
                        <td class="text-right">£{{ number_format($grossAmount, 2) }}</td>
                    </tr>
                    @if($processingFee > 0)
                        <tr>
                            <td class="label">Processing Fees</td>
                            <td class="text-right">-£{{ number_format($processingFee, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td><strong>TOTAL PAID</strong></td>
                        <td class="text-right"><strong>£{{ number_format($finalTotal, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"
                            style="font-size: 11px; color: #666; padding-top: 10px; border-top: 1px dashed #ccc;">

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
    </div>

</body>

</html>