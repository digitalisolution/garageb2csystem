<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .email-container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #0073e6; color: white; padding: 10px; text-align: center; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #666; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .total { font-weight: bold; background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Enquiry Confirmation #{{ $estimate->id }}</h2>
        </div>

        <p>Hello {{ $customer['customer_name'] ?? 'Customer' }},</p>

        <p>Thank you for submitting your enquiry. We have received your request and will contact you shortly.</p>

        <h4><strong>Estimate ID:</strong> {{ $estimate->id }}</h4>
        <h4><strong>Vehicle Registration:</strong> {{ $estimate->vehicle_reg_number ?? 'N/A' }}</h4>

        <h4><strong>Services:</strong></h4>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workshopProducts as $service)
                    <tr>
                        <td>{{ $service->service_name }}</td>
                        <td>£{{ number_format($service->service_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total">
                    <td><strong>Total</strong></td>
                    <td><strong>£{{ number_format($estimate->grandTotal ?? 0, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <p>If you have any questions, feel free to contact us at any time.</p>

        <p>Best regards,<br>{{ $garage->garage_name ?? config('mail.from.name') }}</p>

        <div class="footer">
            &copy; {{ now()->year }} {{ $garage->garage_name ?? config('mail.from.name') }}<br>
            {{ $garage->address ?? '123 Garage Street, Birmingham' }}<br>
            {{ $garage->email ?? config('mail.from.address') }}
        </div>
    </div>
</body>
</html>