<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome Email</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
  <div style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    
    <!-- Header -->
    <div style="background:#1d4ed8;padding:30px;text-align:center;color:#ffffff;">
      <h1 style="margin:0;font-size:24px;font-weight:bold;">Welcome to {{ $garage->garage_name ?? 'Garage Solutions' }} 🚗</h1>
    </div>
    
    <!-- Body -->
    <div style="padding:30px;color:#333333;line-height:1.6;">
      <h3 style="margin-top:0;font-size:20px;color:#111827;">Hi {{ $customer['name'] }} {{ $customer['last_name'] }},</h3>
      <p style="margin:0 0 20px;">Thank you for registering with us! We’re excited to have you on board. Here are the details you provided:</p>
      
      <!-- Details Card -->
      <div style="background:#f9fafb;padding:20px;border-radius:10px;border:1px solid #e5e7eb;">
         <p style="margin:6px 0;"><strong>Name:</strong> {{ $customer['name'] }} {{ $customer['last_name'] }}</p>
        <p style="margin:6px 0;"><strong>Email:</strong> {{ $customer['email'] }}</p>
        <p style="margin:6px 0;"><strong>Phone:</strong> {{ $customer['phone'] }}</p>
        <p style="margin:6px 0;"><strong>Company:</strong> {{ $customer['company_name'] }}</p>
        <p style="margin:6px 0;"><strong>Address:</strong><br>
          {{ $customer['address_street'] }}, {{ $customer['address_city'] }}<br>
          {{ $customer['address_postcode'] }}, {{ $customer['address_county'] }}<br>
          {{ $customer['address_country'] }}
        </p>
        <p style="margin:6px 0;"><strong>Registered At:</strong> {{ $customer['registered_at'] }}</p>
        <p style="margin:6px 0;"><strong>IP Address:</strong> {{ $customer['ip_address'] }}</p>
      </div>
      
      <p style="margin:25px 0 0;">If any of these details are incorrect, please <a href="{{ url('/customer/myaccount') }}" style="color:#1d4ed8;text-decoration:none;">update your profile</a>.</p>
    </div>
    
    <!-- CTA -->
    <div style="text-align:center;padding:20px;">
      <a href="{{ url('/') }}" style="display:inline-block;background:#1d4ed8;color:#ffffff;text-decoration:none;padding:12px 30px;border-radius:8px;font-size:16px;font-weight:bold;">
        Visit Website
      </a>
    </div>
    
    <!-- Footer -->
    <div style="background:#f9fafb;padding:20px;text-align:center;font-size:13px;color:#6b7280;">
      <p style="margin:0;">Best regards,<br>{{ $garage->garage_name ?? 'Garage Solutions Team' }}</p>
      <p style="margin:10px 0 0;font-size:12px;">© {{ date('Y') }} {{ $garage->garage_name ?? 'Garage Solutions' }}. All rights reserved.</p>
    </div>
    
  </div>
</body>
</html>
