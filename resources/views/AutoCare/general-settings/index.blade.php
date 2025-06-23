@extends('samples')
@section('content')
@section('content')
    <div class="container">
        <h2>General Settings</h2>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp" type="button"
                    role="tab" aria-controls="smtp" aria-selected="true">SMTP Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button"
                    role="tab" aria-controls="payment" aria-selected="false">Payment Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" type="button"
                    role="tab" aria-controls="booking" aria-selected="false">Booking Color Settings</button>
            </li>
            <!-- Add more tabs here if needed -->
        </ul>
        <!-- Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('message.level'))
            <div class="alert alert-{{ session('message.level') }} alert-dismissible"
                onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h4>
                {!! session('message.content') !!}
            </div>
        @endif
        <!-- Tab Content -->
        <div class="tab-content mt-3" id="settingsTabsContent">
            <!-- SMTP Settings Tab -->
            <div class="tab-pane fade show active" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                @include('AutoCare.general-settings.partials.smtp_setting') <!-- Include SMTP settings -->
            </div>

            <!-- Payment Settings Tab -->
            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                @include('AutoCare.general-settings.partials.payment_setting') <!-- Include Payment settings -->
            </div>

            <!-- booking Settings Tab -->
            <div class="tab-pane fade" id="booking" role="tabpanel" aria-labelledby="booking-tab">
                @include('AutoCare.general-settings.partials.booking_setting') <!-- Include booking settings -->
            </div>

            <!-- Add more tabs here if needed -->
        </div>
    </div>
@endsection