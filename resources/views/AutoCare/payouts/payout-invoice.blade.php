<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Garage Payout Invoice #{{ $payout->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 40px; }
        .info { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .footer { margin-top: 60px; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Garage Payout Invoice</h1>
        <p>Date: {{ $date }}</p>
        <p>Revolut Transaction ID: {{ $transactionId }}</p>
    </div>

    <div class="info">
        <strong>Garage:</strong> {{ $payout->garage->garage_name }}<br>
        <strong>Workshop ID:</strong> #{{ $payout->workshop->id }}<br>
        <strong>Invoice ID:</strong> {{ $payout->id }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Garage Settlement – Workshop #{{ $payout->workshop->id }}</td>
                <td class="text-right">£{{ number_format($payout->payout_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Total Paid</th>
                <th class="text-right">£{{ number_format($payout->payout_amount, 2) }}</th>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Thank you for your partnership!
    </div>
</body>
</html>