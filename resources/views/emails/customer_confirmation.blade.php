<!DOCTYPE html>
<html>

<head>
    <title>Contact Form Inquiry</title>
</head>

<body>
<style type="text/css">p{color:#333;}</style>
<div style="background:#ddd; padding:50px;">
	<div style="background:#fff;padding:30px;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.3);margin:0 auto;max-width:400px;display:inline-block;">
  <p>Dear {{ $name }},</p>

<p>Thank you for reaching out to us. We have received your message:</p>
<div style="background:#eee;padding:10px 20px;border-radius:4px;">
    <p>{{ $user_message }}</p>
</div>

<p>We will get back to you shortly.</p>

<p>Best regards,<br>{{ config('mail.from.name') }}</p>
</div>
</div>
</body>

</html>