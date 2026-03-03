<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; }
        .button { display: inline-block; padding: 12px 30px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $garage->garage_name }}</h1>
    </div>
    
    <div class="content">
        <h2>Dear {{ $garageName }},</h2>
        
        <p>Your garage payout invoice is ready.</p>
        
        <table style="width: 100%; margin: 20px 0;">
            <tr><td><strong>Invoice Number:</strong></td><td>{{ $invoiceNumber }}</td></tr>
            <tr><td><strong>Amount:</strong></td><td>{{ $amount }}</td></tr>
            <tr><td><strong>Date:</strong></td><td>{{ $date }}</td></tr>
        </table>
        
        <p>The PDF invoice is attached to this email for your records.</p>
        
        <a href="{{ route('garage.payoutInvoices.view', $invoice) }}" class="button">View Invoice Online</a>
        
        <p>Thank you for your partnership!</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply.</p>
    </div>
</body>
</html>