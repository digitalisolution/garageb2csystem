@extends('layouts.app')

@section('content')
    <div class="order_success mt-60 mb-60 text-center">
        <img src="{{ asset('frontend/themes/default/img/tick-success.gif') }}" class="img-adjust" alt="Success">
        <h3>Order Confirmed <br>Thank You for Your Purchase!</h3>
        <p>Your order has been successfully placed! <br>A confirmation email with the details is on its way.</p>

        <!-- Show additional message for new customers -->
        @if ($isNewCustomer)
            <div class="alert alert-info mt-4">
                <p><strong>Welcome to our platform!</strong></p>
                <p>An account has been created for you. A temporary password has been sent to your email. Please log in and
                    update your password for security reasons.</p>
            </div>
        @endif
    </div>

    <script>
        // Prevent going back after success
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.location.href = "{{ route('home') }}"; // Redirect to home if back is pressed
        };

        // Automatically redirect to home after 5 seconds
        setTimeout(function () {
            window.location.href = "{{ route('home') }}";
        }, 5000);
    </script>
@endsection