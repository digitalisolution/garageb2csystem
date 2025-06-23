<!DOCTYPE html>
<html>

<head>
    <title>Your Account Password</title>
</head>

<body>
    <p>Hello,</p>
    <p>Your account has been created successfully. Here are your login details:</p>
    <ul>
        <li><strong>Email:</strong> {{ $customerEmail }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
    </ul>
    <p>Please log in to your account and change this password for security reasons.</p>
</body>

</html>