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
              <td bgcolor="#fff">{{ $name }} {{ $last_name }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Email:</strong></td>
              <td bgcolor="#fff">{{ $email }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Phone:</strong></td>
              <td bgcolor="#fff">{{ $phone }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Registered At:</strong></td>
              <td bgcolor="#fff">{{ $registered_at }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>Address:</strong></td>
              <td bgcolor="#fff">{{ $address_street }}, {{ $address_city }}, {{ $address_postcode }}, {{ $address_county }}, {{ $address_country }}</td>
            </tr>
            <tr>
              <td bgcolor="#fff"><strong>IP Address:</strong></td>
              <td bgcolor="#fff">{{ $ip_address }}</td>
            </tr>

          </table>
        <p style="margin-top:25px;">We are looking into their request and a member of the team will be in touch within 2–3 working days.</p>
      </div>
        <div style="background:#ddd;padding:30px;text-align:center;">
        Best regards,<br>Garage Admin
        </div>
        </div>
    </div>
</body>
</html>