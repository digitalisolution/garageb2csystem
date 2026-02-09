<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body>
	<div style="background:#eee;padding:40px;">
		<div class="mailbox" style="background:#fff;padding:30px;border-radius:10px;margin:auto;width:350px;text-align:center;">
		    <h2 style="color:#000;">Hello,</h2>
		    <p style="color:#333;">Your One-Time Password (OTP) is:</p>
		    <h1 style="color:#355cce;background:#eee;padding:5px 20px;border-radius:6px;">{{ $otp }}</h1>
		    <p style="color:#333;">This code is valid for the next <strong>5 minutes.</strong></p>
		    <p style="color:#333;">If you did not request this code, please disregard this message.</p>
		    <br>
		    <p style="color:#333;">Thanks,<br><strong>{{ getGarageDetails()->garage_name }} Team</strong></p>
	    </div>
    </div>
</body>
</html>
