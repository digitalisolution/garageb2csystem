<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Appointment Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding:30px;border:solid 1px #ccc;">
        <div style="background-color:#151515; padding:20px;border-radius:4px;text-align:center;">
        <h1 style="color: #fff;margin:0;">New Appointment Received</h1>
        <h3 style="color: #fff;margin:0;"><strong>Submitted On:</strong> {{ now()->format('Y-m-d H:i') }}</h3>
        </div>
        <h3 style="color:#151515;margin-bottom:5px;text-align:center;">Customer Details</h3>
        <table width="100%" border="0" cellspacing="1" cellpadding="5" bgcolor="#CCCCCC">
          <tr>
            <th bgcolor="#626262" style="color:#fff;">Name</th>
            <th bgcolor="#626262" style="color:#fff;">Email</th>
            <th bgcolor="#626262" style="color:#fff;">Phone</th>
          </tr>
          <tr>
            <td bgcolor="#eee" align="center">{{ $appointment['first_name'] }} {{ $appointment['last_name'] }}</td>
            <td bgcolor="#eee" align="center">{{ $appointment['email'] }}</td>
            <td bgcolor="#eee" align="center">{{ $appointment['phone'] }}</td>
          </tr>
        </table>
        

        <h3 style="color:#151515;margin-bottom:5px;text-align:center;">Vehicle Information</h3>
        <table width="100%" border="0" cellspacing="1" cellpadding="5" bgcolor="#CCCCCC">
          <tr>
            <th bgcolor="#626262" style="color:#fff;">Type</th>
            <th bgcolor="#626262" style="color:#fff;">Make</th>
            <th bgcolor="#626262" style="color:#fff;">Model</th>
            <th bgcolor="#626262" style="color:#fff;">Year</th>
          </tr>
          <tr>
            <td bgcolor="#eee" align="center">{{ $appointment['vehicle_type'] }}</td>
            <td bgcolor="#eee" align="center">@if(!empty($appointment['vehicle_model']))
            {{ $appointment['vehicle_make'] }}
        @endif</td>
            <td bgcolor="#eee" align="center">@if(!empty($appointment['vehicle_model']))
            {{ $appointment['vehicle_model'] }}
        @endif</td>
        <td bgcolor="#eee" align="center">@if(!empty($appointment['vehicle_year']))
            {{ $appointment['vehicle_year'] }}
        @endif</td>
          </tr>
        </table>
        
        <h3 style="color:#151515;margin-bottom:5px;text-align:center;">Appointment Details</h3>
        <table width="100%" border="0" cellspacing="1" cellpadding="5" bgcolor="#CCCCCC">
          <tr>
            <th bgcolor="#626262" style="color:#fff;">Date</th>
            <th bgcolor="#626262" style="color:#fff;">Time</th>
          </tr>
          <tr>
            <td bgcolor="#eee" align="center">{{ $appointment['choose_date'] }}</td>
            <td bgcolor="#eee" align="center">{{ $appointment['choose_time'] }}</td>
          </tr>
        </table>

        @if(!empty($appointment['message']))
            <h3 style="color:#151515;margin-bottom:5px;text-align:center;">Message</h3>
            <p style="text-align:center;border:solid 1px #ddd;padding:6px;">{{ $appointment['message'] }}</p>
        @endif

        
        <p style="font-size: 14px; color: #888; text-align:center;">
            You can reply directly to this email to contact the customer.
        </p>
    </div>
</body>
</html>
