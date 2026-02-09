<!DOCTYPE html>
<html>
<head>
    <title>New Customer Registered</title>
</head>
<body>

    <div style="background-color:#f7f7f7;margin:0;padding:40px 0;">
        <div style="background:#fff; border-radius:10px;width:500px;margin:auto;overflow:hidden;">
        <div style="background:#11acd3;color:#fff;font-size:24px;font-weight:bold;text-align:center;text-transform:uppercase;padding:20px 0;">A new customer has registered</div>
        <div style="padding:30px;">
          <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#ddd">
            <tr>
              <td bgcolor="#fff"><strong>Name:</strong></td>
              <td bgcolor="#fff">{{ $customer['name'] }} {{ $customer['last_name'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Email:</strong></td>
              <td bgcolor="#fff">{{ $customer['email'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Phone:</strong></td>
              <td bgcolor="#fff">{{ $customer['phone'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Company:</strong></td>
              <td bgcolor="#fff">{{ $customer['company_name'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Registered At:</strong></td>
              <td bgcolor="#fff">{{ $customer['registered_at'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Address:</strong></td>
              <td bgcolor="#fff">{{ $customer['address_street'] }}, {{ $customer['address_city'] }}, {{ $customer['address_postcode'] }}, {{ $customer['address_county'] }}, {{ $customer['address_country'] }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>IP Address:</strong></td>
              <td bgcolor="#fff">{{ $customer['ip_address'] }}</td>
            </tr>
          </table>
        <p style="margin-top:25px;">We are looking into their request and a member of the team will be in touch within 2–3 working days.</p>
      </div>
        <div style="background:#ddd;padding:30px;text-align:center;">
        Best regards,<br>{{ $garage->garage_name ?? $garage->name ?? 'Garage Admin' }}
        </div>
        </div>
    </div>
</body>
</html>