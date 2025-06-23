
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Appointment Notification</title>

</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 400px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding: 20px;text-align:center;">
        <img src="https://www.digitalideasltd.in/frontend/themes/default/img/checked-icon.gif" alt="checked-icon" width="auto" height="100">
    	<h5 style="color:#151515;">Hay {{ $appointment['first_name'] }} {{ $appointment['last_name'] }},</h5>
		<h3 style="color:#151515;">Your appointment has been submitted successfully.</h3>
            <div style="border:solid 1px rgba(0,0,0,0.3);padding:5px 10px;border-radius:4px;margin:5px 0;color:#151515;"><strong>Date:</strong> {{ $appointment['choose_date'] }}</div>
            <div style="border:solid 1px rgba(0,0,0,0.3);padding:5px 10px;border-radius:4px;margin:5px 0;color:#151515;"><strong>Time:</strong> {{ $appointment['choose_time'] }}</div>
       		<p style="color:#151515;">We'll contact you shortly!</p>
        <hr>
        <p style="font-size: 14px; color: #888;">This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>
