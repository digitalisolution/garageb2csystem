@extends('samples')
@section('content')
    <div class="container-fluid">
        <!-- Parent Tabs -->
        <ul class="nav nav-tabs" id="parentTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="module-settings-tab" data-bs-toggle="tab"
                    data-bs-target="#module-settings" type="button" role="tab" aria-controls="module-settings"
                    aria-selected="true">Module Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="smtp-details-tab" data-bs-toggle="tab" data-bs-target="#smtp-details"
                    type="button" role="tab" aria-controls="smtp-details" aria-selected="false">SMTP Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payment-details-tab" data-bs-toggle="tab" data-bs-target="#payment-details"
                    type="button" role="tab" aria-controls="payment-details" aria-selected="false">Payment Gateway</button>
            </li>
        </ul>

        <!-- Parent Tab Content -->
        <div class="tab-content" id="parentTabContent">
            <!-- Module Settings Tab -->
            <div class="tab-pane fade show active" id="module-settings" role="tabpanel"
                aria-labelledby="module-settings-tab">
                <!-- Child Tabs for Tyre and Service -->
                <ul class="nav nav-tabs mt-3" id="childTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tyre-tab" data-bs-toggle="tab" data-bs-target="#tyre"
                            type="button" role="tab" aria-controls="tyre" aria-selected="true">Tyre</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service"
                            type="button" role="tab" aria-controls="service" aria-selected="false">Service</button>
                    </li>
                </ul>

                <!-- Child Tab Content -->
                <div class="tab-content" id="childTabContent">
                    <!-- Tyre Tab -->
                    <div class="tab-pane fade show active" id="tyre" role="tabpanel" aria-labelledby="tyre-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Enable/Disable Option</h5>
                                    <div class="bg-white p-3 rounded height-130">
                                        <div class="form-check form-switch">
                                            <label class="form-check-label" for="tyreEnable">
                                                <input class="form-check-input" type="radio" name="tyreEnableDisable"
                                                    id="tyreEnable" value="enable" checked>
                                                Enable</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <label class="form-check-label" for="tyreDisable">
                                                <input class="form-check-input" type="radio" name="tyreEnableDisable"
                                                    id="tyreDisable" value="disable">
                                                Disable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Search Type</h5>
                                    <div class="bg-white p-3 rounded height-130">
                                        <div class="form-check">
                                            <label class="form-check-label" for="tyreVrm"><input class="form-check-input"
                                                    type="checkbox" id="tyreVrm" name="searchType" value="vrm">
                                                VRM</label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label" for="tyreManual"><input class="form-check-input"
                                                    type="checkbox" id="tyreManual" name="searchType" value="manual">
                                                Manual</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Tyre Order Type</h5>
                                    <div class="bg-white p-3 rounded height-130">
                                        <div class="form-check">
                                            <label class="form-check-label" for="mailOrder"><input class="form-check-input"
                                                    type="checkbox" id="mailOrder" name="tyreOrderType" value="mailOrder">
                                                Mail Order</label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label" for="mobileFitted"><input
                                                    class="form-check-input" type="checkbox" id="mobileFitted"
                                                    name="tyreOrderType" value="mobileFitted">
                                                Mobile Fitted</label>
                                        </div>
                                        <div class="form-check">
                                            <label class="form-check-label" for="fullyFitted"><input
                                                    class="form-check-input" type="checkbox" id="fullyFitted"
                                                    name="tyreOrderType" value="fullyFitted">
                                                Fully Fitted</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Payment Mode</h5>
                                    <div class="row">
                                        <div class="mb-2 col-md-4">
                                            <div class="bg-white p-3 rounded">
                                                <strong>Mail Order:</strong>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mailOrderOffline"
                                                        name="mailOrderPayment" value="offline">
                                                    <label class="form-check-label" for="mailOrderOffline">Offline</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mailOrderOnline"
                                                        name="mailOrderPayment" value="online">
                                                    <label class="form-check-label" for="mailOrderOnline">Online</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2 col-md-4">
                                            <div class="bg-white p-3 rounded">
                                                <strong>Mobile Fitted:</strong>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mobileFittedOffline"
                                                        name="mobileFittedPayment" value="offline">
                                                    <label class="form-check-label"
                                                        for="mobileFittedOffline">Offline</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="mobileFittedOnline"
                                                        name="mobileFittedPayment" value="online">
                                                    <label class="form-check-label" for="mobileFittedOnline">Online</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2 col-md-4">
                                            <div class="bg-white p-3 rounded">
                                                <strong>Fully Fitted:</strong>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="fullyFittedOffline"
                                                        name="fullyFittedPayment" value="offline">
                                                    <label class="form-check-label" for="fullyFittedOffline">Offline</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="fullyFittedOnline"
                                                        name="fullyFittedPayment" value="online">
                                                    <label class="form-check-label" for="fullyFittedOnline">Online</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Tab -->
                    <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Enable/Disable Option</h5>
                                    <div class="bg-white p-3 rounded">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="radio" name="serviceEnableDisable"
                                                id="serviceEnable" value="enable" checked>
                                            <label class="form-check-label" for="serviceEnable">Enable</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="radio" name="serviceEnableDisable"
                                                id="serviceDisable" value="disable">
                                            <label class="form-check-label" for="serviceDisable">Disable</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Search Type</h5>
                                    <div class="bg-white p-3 rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="serviceVrm"
                                                name="searchType" value="vrm">
                                            <label class="form-check-label" for="serviceVrm">VRM</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="serviceManual"
                                                name="searchType" value="manual">
                                            <label class="form-check-label" for="serviceManual">Manual</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <h5>Payment Mode</h5>
                                    <div class="bg-white p-3 rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="fullyFittedOffline"
                                                name="fullyFittedPayment" value="offline">
                                            <label class="form-check-label" for="fullyFittedOffline"> Offline</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="fullyFittedOnline"
                                                name="fullyFittedPayment" value="online">
                                            <label class="form-check-label" for="fullyFittedOnline">Online</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMTP Details Tab -->
        <div class="tab-content">
            <div class="tab-pane fade" id="smtp-details" role="tabpanel" aria-labelledby="smtp-details-tab">
                <h5>SMTP Configuration</h5>
                <form>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="smtpHost" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="smtpHost" placeholder="Enter SMTP Host">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="smtpPort" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="smtpPort" placeholder="Enter SMTP Port">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="smtpUsername" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="smtpUsername" placeholder="Enter SMTP Username">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="smtpPassword" class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" id="smtpPassword" placeholder="Enter SMTP Password">
                        </div>
                    </div>
                    <div class="text-right"><button type="submit" class="btn btn-primary">Save Changes</button></div>
                </form>
            </div>
        </div>


        <!-- Payment Details Tab -->


        <div class="mt-4">
            <h5>Payment Gateway Settings</h5>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="paymentGatewayTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="globalpay-tab" data-bs-toggle="tab" data-bs-target="#globalpay"
                        type="button" role="tab" aria-controls="globalpay" aria-selected="true">Globalpay</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="dojo-tab" data-bs-toggle="tab" data-bs-target="#dojo" type="button"
                        role="tab" aria-controls="dojo" aria-selected="false">Dojo</button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="paymentGatewayTabsContent">
                <!-- Globalpay Tab -->
                <div class="tab-pane fade show active" id="globalpay" role="tabpanel" aria-labelledby="globalpay-tab">
                    <form action="" method="POST">
                        @csrf
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5>Globalpay Configuration</h5>

                                <!-- Enable/Disable -->
                                <div class="mb-3">
                                    <label class="form-label">Active</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_active"
                                            id="globalpay_enable" value="1" checked>
                                        <label class="form-check-label" for="globalpay_enable">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_active"
                                            id="globalpay_disable" value="0">
                                        <label class="form-check-label" for="globalpay_disable">No</label>
                                    </div>
                                </div>

                                <!-- Merchant ID -->
                                <div class="mb-3">
                                    <label for="globalpay_merchant_id" class="form-label">Merchant ID</label>
                                    <input type="text" class="form-control" id="globalpay_merchant_id"
                                        name="globalpay_merchant_id" placeholder="Enter Merchant ID">
                                </div>

                                <!-- Secret Key -->
                                <div class="mb-3">
                                    <label for="globalpay_secret_key" class="form-label">Secret Key</label>
                                    <input type="password" class="form-control" id="globalpay_secret_key"
                                        name="globalpay_secret_key" placeholder="Enter Secret Key">
                                </div>

                                <!-- Test Mode -->
                                <div class="mb-3">
                                    <label class="form-label">Enable Test Mode</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_test_mode"
                                            id="globalpay_test_mode_enable" value="1">
                                        <label class="form-check-label" for="globalpay_test_mode_enable">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_test_mode"
                                            id="globalpay_test_mode_disable" value="0" checked>
                                        <label class="form-check-label" for="globalpay_test_mode_disable">No</label>
                                    </div>
                                </div>

                                <!-- Currencies -->
                                <div class="mb-3">
                                    <label for="globalpay_currencies" class="form-label">Currencies
                                        (comma-separated)</label>
                                    <input type="text" class="form-control" id="globalpay_currencies"
                                        name="globalpay_currencies" placeholder="e.g., GBP,USD,EUR" value="GBP">
                                </div>

                                <!-- Currency Code -->
                                <div class="mb-3">
                                    <label for="globalpay_currency_code" class="form-label">Currency Code</label>
                                    <input type="text" class="form-control" id="globalpay_currency_code"
                                        name="globalpay_currency_code" placeholder="e.g., 826" value="826">
                                </div>

                                <!-- Display on Website -->
                                <div class="mb-3">
                                    <label class="form-label">Display on Website</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_display_website"
                                            id="globalpay_display_website_yes" value="1">
                                        <label class="form-check-label" for="globalpay_display_website_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_display_website"
                                            id="globalpay_display_website_no" value="0" checked>
                                        <label class="form-check-label" for="globalpay_display_website_no">No</label>
                                    </div>
                                </div>

                                <!-- Selected by Default on Invoice -->
                                <div class="mb-3">
                                    <label class="form-label">Selected by Default on Invoice</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_default_invoice"
                                            id="globalpay_default_invoice_yes" value="1">
                                        <label class="form-check-label" for="globalpay_default_invoice_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="globalpay_default_invoice"
                                            id="globalpay_default_invoice_no" value="0" checked>
                                        <label class="form-check-label" for="globalpay_default_invoice_no">No</label>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Dojo Tab -->
                <div class="tab-pane fade" id="dojo" role="tabpanel" aria-labelledby="dojo-tab">
                    <form action="" method="POST">
                        @csrf
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5>Dojo Configuration</h5>

                                <!-- Enable/Disable -->
                                <div class="mb-3">
                                    <label class="form-label">Active</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_active" id="dojo_enable"
                                            value="1" checked>
                                        <label class="form-check-label" for="dojo_enable">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_active" id="dojo_disable"
                                            value="0">
                                        <label class="form-check-label" for="dojo_disable">No</label>
                                    </div>
                                </div>

                                <!-- Merchant ID -->
                                <div class="mb-3">
                                    <label for="dojo_merchant_id" class="form-label">Merchant ID</label>
                                    <input type="text" class="form-control" id="dojo_merchant_id" name="dojo_merchant_id"
                                        placeholder="Enter Merchant ID">
                                </div>

                                <!-- Secret Key -->
                                <div class="mb-3">
                                    <label for="dojo_secret_key" class="form-label">Secret Key</label>
                                    <input type="password" class="form-control" id="dojo_secret_key" name="dojo_secret_key"
                                        placeholder="Enter Secret Key">
                                </div>

                                <!-- Test Mode -->
                                <div class="mb-3">
                                    <label class="form-label">Enable Test Mode</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_test_mode"
                                            id="dojo_test_mode_enable" value="1">
                                        <label class="form-check-label" for="dojo_test_mode_enable">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_test_mode"
                                            id="dojo_test_mode_disable" value="0" checked>
                                        <label class="form-check-label" for="dojo_test_mode_disable">No</label>
                                    </div>
                                </div>

                                <!-- Currencies -->
                                <div class="mb-3">
                                    <label for="dojo_currencies" class="form-label">Currencies (comma-separated)</label>
                                    <input type="text" class="form-control" id="dojo_currencies" name="dojo_currencies"
                                        placeholder="e.g., GBP,USD,EUR" value="GBP">
                                </div>

                                <!-- Currency Code -->
                                <div class="mb-3">
                                    <label for="dojo_currency_code" class="form-label">Currency Code</label>
                                    <input type="text" class="form-control" id="dojo_currency_code"
                                        name="dojo_currency_code" placeholder="e.g., 826" value="826">
                                </div>

                                <!-- Display on Website -->
                                <div class="mb-3">
                                    <label class="form-label">Display on Website</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_display_website"
                                            id="dojo_display_website_yes" value="1">
                                        <label class="form-check-label" for="dojo_display_website_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_display_website"
                                            id="dojo_display_website_no" value="0" checked>
                                        <label class="form-check-label" for="dojo_display_website_no">No</label>
                                    </div>
                                </div>

                                <!-- Selected by Default on Invoice -->
                                <div class="mb-3">
                                    <label class="form-label">Selected by Default on Invoice</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_default_invoice"
                                            id="dojo_default_invoice_yes" value="1">
                                        <label class="form-check-label" for="dojo_default_invoice_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dojo_default_invoice"
                                            id="dojo_default_invoice_no" value="0" checked>
                                        <label class="form-check-label" for="dojo_default_invoice_no">No</label>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('AutoCare.general-settings.partials.calendar_setting')
    </div>
    </div>
@endsection

<style type="text/css">
    .form-check-label {
        padding-left: initial !important;
    }

    .height-130 {
        min-height: 130px;
    }
</style>