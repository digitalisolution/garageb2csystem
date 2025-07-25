<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body>
    <h2>Hello,</h2>
    <p>Your One-Time Password (OTP) is:</p>
    <h1 style="color: #2c3e50;">{{ $otp }}</h1>
    <p>This code will expire in 5 minutes.</p>
    <p>If you didn’t request this, you can ignore this email.</p>

    <br>
    <p>Thanks,<br>{{ getGarageDetails()->garage_name }} Team</p>
</body>
</html>
