<!DOCTYPE html>
<html>
<head>
    <title>Welcome!</title>
</head>
<body>
    <div style="background:#eee;padding:40px;">
        <div class="mailbox" style="background:#fff;padding:30px;border-radius:10px;margin:auto;width:350px;text-align:center;">
            <h3>Hi {{ $name }} {{ $last_name }},</h3>
            <p>Thank you for registering with us.</p>
            <p><strong>Your Details:</strong></p>
            <div style="background:#eee;padding:20px;border-radius:10px;text-align:left;">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Phone:</strong> {{ $phone }}</p>
                <p><strong>Address:</strong>
                    {{ $address_street }}, {{ $address_city }}, {{ $address_postcode }}, {{ $address_county }}, {{ $address_country }}
                </p>
                <p><strong>IP Address:</strong> {{ $ip_address }}</p>

                </div>

            <h5>Best regards,</h5>
            <p>The Garage Team</p>
        </div>
    </div>
</body>
</html>