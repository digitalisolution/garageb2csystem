@extends('samples')
@section('content')
    <section class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
        <div class="row">
            <div class="col-sm-12" id="HideForShowProduct">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> General Settings
                    </div>
                    <div class="card-body table-responsive"
                        style="font-size: 13px;padding-left:10px;vertical-align:middle;width:100%;">
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
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tyres-service-tab" data-bs-toggle="tab" data-bs-target="#tyres-service" type="button" role="tab" aria-controls="tyres-service" aria-selected="false">Tyre/Service Module</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="common-module-tab" data-bs-toggle="tab" data-bs-target="#common-module" type="button" role="tab" aria-controls="common-module" aria-selected="false">Common Module</button>
                            </li>
                            <!-- Add more tabs here if needed -->
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="settingsTabsContent">
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

                            <!-- Tyre/Service Settings Tab -->
                            <div class="tab-pane fade" id="tyres-service" role="tabpanel" aria-labelledby="tyres-service-tab">
                                @include('AutoCare.general-settings.partials.tyres_service_module_setting')
                            </div>

                            <!-- Common Settings Tab -->
                            <div class="tab-pane fade" id="common-module" role="tabpanel" aria-labelledby="common-module-tab">
                                @include('AutoCare.general-settings.partials.common_module_setting')
                            </div>

                            <!-- Add more tabs here if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection