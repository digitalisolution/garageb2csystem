<div id="otp-verification-wrapper">
    <div class="form-group mb-2">
       <input type="email" name="email" id="email" placeholder="Email*" class="form-control" value="{{ old('email') }}" required>
       <div id="otpMessage" class="text-sm"></div>
        <button type="button" id="sendOtpBtn" class="btn btn-sm btn-primary mt-0">Send OTP</button>
    </div>

    <div id="otpField" style="display:none;" class="form-group mt-2">
        <label for="otp_code">Enter OTP</label>
       <input type="text" name="otp_code" id="otp_code" class="form-control" required>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('sendOtpBtn').addEventListener('click', function () {
    const email = document.getElementById('email').value;
    const messageDiv = document.getElementById('otpMessage');

    // Reset message
    messageDiv.innerHTML = '';
    messageDiv.className = 'text-sm mt-1';

    if (!email) {
        messageDiv.textContent = 'Please enter a valid email before sending OTP.';
        messageDiv.classList.add('text-danger');
        return;
    }

    fetch("{{ route('otp.send') }}", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ email })
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok) {
            // Status 200
            messageDiv.textContent = data.message || "OTP sent successfully.";
            messageDiv.classList.add('text-success');
            document.getElementById('otpField').style.display = 'block';
        } else {
            // Any non-200 status
            messageDiv.textContent = data.message || "Something went wrong. Please try again.";
            messageDiv.classList.add('text-danger');
        }
    })
    .catch(() => {
        // Network or unexpected failure
        messageDiv.textContent = "Network error. Please check your connection.";
        messageDiv.classList.add('text-danger');
    });
});
</script>

@endpush

