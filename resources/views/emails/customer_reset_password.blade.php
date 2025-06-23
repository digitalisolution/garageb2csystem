<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $garage->garage_name }} - Password Reset Request</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div
        style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <h1 style="font-size: 24px; margin-bottom: 20px;">Hello!</h1>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p style="text-align: center; margin: 20px 0;">
            <a href="{{ $resetUrl }}"
                style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Reset
                Password</a>
        </p>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web
            browser:</p>
        <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
        <p style="margin-top: 20px;">Regards,<br>{{ $garage->garage_name }}</p>
    </div>
</body>

</html>