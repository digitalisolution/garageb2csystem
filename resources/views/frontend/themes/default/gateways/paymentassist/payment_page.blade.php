{{-- resources/views/gateways/paymentassist/payment_page_website.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Payment for Job {{ $job->id ?? 'N/A' }}</title> {{-- Adjust title --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Include your CSS framework (Bootstrap, etc.) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body.gateway-2checkout {
            /* Add styles similar to your CI layout if needed */
        }

        .mtop30 {
            margin-top: 30px;
        }

        .mbot30 {
            margin-bottom: 30px;
        }

        .panel_s {
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .panel-body {
            padding: 15px;
        }

        .no-margin {
            margin: 0;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body class="gateway-2checkout">
    <div class="container">
        <div class="row justify-content-center"> {{-- Use BS5 classes --}}
            <div class="col-md-8 mtop30"> {{-- Adjusted column and margin --}}
                <div class="mbot30 text-center">
                    {{-- Payment Gateway Logo --}}
                    <h2>{{ config('app.name', 'Your App Name') }}</h2> {{-- Replace with your logo or app name --}}
                </div>
                <div class="row">
                    <div class="panel panel-default panel_s"> {{-- Adjusted BS classes --}}
                        <div class="panel-body">
                            <h4 class="no-margin">
                                {{-- Payment for Job/Invoice --}}
                                Payment for Job #{{ $job->id ?? 'N/A' }}
                            </h4>
                            <hr />
                            <p><span class="bold">Payment Total: £{{ number_format($total, 2) }}</span></p> {{-- Format
                            currency --}}
                            <p><span class="bold">Billing Email: {{ $billing_email ?? 'N/A' }}</span></p>

                            {{-- Payment Form --}}
                            <form id="paymentassist_form_website" action="{{ route('paymentassist.initiate.website') }}"
                                method="POST">
                                @csrf {{-- Always include CSRF token --}}
                                <input type="hidden" name="jobid" value="{{ $job->id ?? '' }}">
                                <input type="hidden" name="total" value="{{ $total }}">

                                <button type="submit" class="btn btn-primary"> {{-- BS5 button class --}}
                                    Submit Payment
                                </button>
                                {{-- Cancel Button - adjust URL as needed --}}
                                <a href="{{ url()->previous() }}" class="btn btn-secondary"> {{-- BS5 button class --}}
                                    Cancel
                                </a>
                                {{-- Or redirect to a specific checkout page --}}
                                {{-- <a href="{{ route('checkout.home') }}" class="btn btn-secondary">Cancel</a> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your JS framework (Bootstrap JS, etc.) if needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>