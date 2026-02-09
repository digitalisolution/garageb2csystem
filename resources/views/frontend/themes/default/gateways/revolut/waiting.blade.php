<!DOCTYPE html>
<html>
<head>
    <title>Processing Payment</title>
</head>
<body>

<h3>Processing your payment, please wait...</h3>

<script>
    const workshopId = {{ $workshop->id }};

    const interval = setInterval(() => {
        fetch(`/check-revolut-status/${workshopId}`)
            .then(res => res.json())
            .then(data => {
                if (data.state === 'COMPLETED') {
                    clearInterval(interval);
                    window.location.href = `/payment-success/${workshopId}`;
                }

                if (data.state === 'FAILED' || data.state === 'CANCELLED') {
                    clearInterval(interval);
                    window.location.href = `/payment-failed/${workshopId}`;
                }
            });
    }, 5000);
</script>

</body>
</html>
