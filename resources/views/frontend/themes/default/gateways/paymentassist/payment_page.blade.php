{{-- resources/views/gateways/paymentassist/payment_page_website.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Payment for Job {{ $job->id ?? 'N/A' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body.gateway-2checkout {
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
        <div class="row justify-content-center">
            <div class="col-md-8 mtop30">
                <div class="mbot30 text-center">

                    <h2>{{ getGarageDetails()->garage_name }}</h2>
                </div>
                <div class="row">
                    <div class="panel panel-default panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin">
                                Payment for Job #{{ $job->id ?? 'N/A' }}
                            </h4>
                            <hr />
                            <p><span class="bold">Payment Total: £{{ number_format($total, 2) }}</span></p>
                            <p><span class="bold">Billing Email: {{ $billing_email ?? 'N/A' }}</span></p>
                            <form id="paymentassist_form_website" action="{{ route('paymentassist.initiate') }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="jobid" value="{{ $job->id ?? '' }}">
                                <input type="hidden" name="total" value="{{ $total }}">

                                <button type="submit" class="btn btn-primary">
                                    Submit Payment
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>