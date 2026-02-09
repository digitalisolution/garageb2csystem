<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f8f9fa; padding:20px;">

    <div style="max-width:600px; margin:auto; background:#ffffff; border:1px solid #ddd; padding:20px; border-radius:8px;">
        <h2 style="color:#333;">Hello {{ $customer['customer_name'] }},</h2>

        <p>Thank you for your booking at 
            <strong>{{ $garage->garage_name ?? config('app.name') }}</strong>.
        </p>

        <p>Your job reference is <strong>JOB-{{ $order->id }}</strong>.</p>

        <p style="font-size:16px; font-weight:bold; color:#28a745;">
            Your New verification code is: {{ $order->verification_code }}
        </p>

        <p>
            Please provide this code to the garage after your service is complete.  
            The garage will use this code to confirm your job has been successfully completed.
        </p>

        <hr>
        <p style="font-size:12px; color:#777;">
            If you didn’t make this booking, please ignore this email.
        </p>
    </div>

</body>
</html>
