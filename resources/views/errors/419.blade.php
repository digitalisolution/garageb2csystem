<!DOCTYPE html>
<html>
<head>
    <title>Session Expired</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
             color: #dc3545;
        }
        p {
            font-size: 18px;
            color: #6c757d;
        }
        .spinner {
             display: inline-block;
             width: 20px;
             height: 20px;
             border: 3px solid rgba(0,0,0,.1);
             border-radius: 50%;
             border-top-color: #0d6efd;
             animation: spin 1s ease-in-out infinite;
             margin-right: 10px;
             vertical-align: text-bottom;
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Session Expired (419)</h1>
        <p id="message">Your session has expired. Redirecting to login...</p>
        <p><span class="spinner"></span> Redirecting in <span id="countdown">1</span> seconds.</p>
        <noscript>
            <p><em>Please enable JavaScript for automatic redirect.</em></p>
            <p><a href="{{ route('webmaster.login') }}">Click here to go to login.</a></p>
        </noscript>
    </div>

    <script>
        function redirectToLogin() {
            window.location.href = "{{ route('webmaster.login') }}";
        }

        // Start the countdown and redirect
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        const messageElement = document.getElementById('message');

        const countdownInterval = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                messageElement.textContent = "Redirecting now...";
                redirectToLogin();
            }
        }, 1000);
        setTimeout(redirectToLogin, 1000);
    </script>
</body>
</html>