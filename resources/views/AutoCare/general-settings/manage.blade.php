@extends('samples')
@section('content')
    <div class="container">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tyre-tab" data-bs-toggle="tab" data-bs-target="#tyre" type="button"
                    role="tab" aria-controls="tyre" aria-selected="true">Tyre</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button"
                    role="tab" aria-controls="service" aria-selected="false">Service</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Tyre Tab -->
            <div class="tab-pane fade show active" id="tyre" role="tabpanel" aria-labelledby="tyre-tab">
                <div class="mt-3">
                    <h5>Enable/Disable Option</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="radio" name="tyreEnableDisable" id="tyreEnable" value="enable"
                            checked>
                        <label class="form-check-label" for="tyreEnable">Enable</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="radio" name="tyreEnableDisable" id="tyreDisable"
                            value="disable">
                        <label class="form-check-label" for="tyreDisable">Disable</label>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Search Type</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="tyreVrm" name="searchType" value="vrm">
                        <label class="form-check-label" for="tyreVrm">VRM</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="tyreManual" name="searchType" value="manual">
                        <label class="form-check-label" for="tyreManual">Manual</label>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Tyre Order Type</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mailOrder" name="tyreOrderType"
                            value="mailOrder">
                        <label class="form-check-label" for="mailOrder">Mail Order</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mobileFitted" name="tyreOrderType"
                            value="mobileFitted">
                        <label class="form-check-label" for="mobileFitted">Mobile Fitted</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="fullyFitted" name="tyreOrderType"
                            value="fullyFitted">
                        <label class="form-check-label" for="fullyFitted">Fully Fitted</label>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Payment Mode</h5>
                    <div class="mb-2">
                        <strong>Mail Order:</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mailOrderOffline" name="mailOrderPayment"
                                value="offline">
                            <label class="form-check-label" for="mailOrderOffline">Offline</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mailOrderOnline" name="mailOrderPayment"
                                value="online">
                            <label class="form-check-label" for="mailOrderOnline">Online</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Mobile Fitted:</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mobileFittedOffline"
                                name="mobileFittedPayment" value="offline">
                            <label class="form-check-label" for="mobileFittedOffline">Offline</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mobileFittedOnline"
                                name="mobileFittedPayment" value="online">
                            <label class="form-check-label" for="mobileFittedOnline">Online</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Fully Fitted:</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fullyFittedOffline"
                                name="fullyFittedPayment" value="offline">
                            <label class="form-check-label" for="fullyFittedOffline">Offline</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fullyFittedOnline" name="fullyFittedPayment"
                                value="online">
                            <label class="form-check-label" for="fullyFittedOnline">Online</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Tab -->
            <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                <div class="mt-3">
                    <h5>Enable/Disable Option</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="radio" name="serviceEnableDisable" id="serviceEnable"
                            value="enable" checked>
                        <label class="form-check-label" for="serviceEnable">Enable</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="radio" name="serviceEnableDisable" id="serviceDisable"
                            value="disable">
                        <label class="form-check-label" for="serviceDisable">Disable</label>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Search Type</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="serviceVrm" name="searchType" value="vrm">
                        <label class="form-check-label" for="serviceVrm">VRM</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="serviceManual" name="searchType" value="manual">
                        <label class="form-check-label" for="serviceManual">Manual</label>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Service Order Type</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mailOrderService" name="serviceOrderType"
                            value="mailOrder">
                        <label class="form-check-label" for="mailOrderService">Mail Order</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="mobileFittedService" name="serviceOrderType"
                            value="mobileFitted">
                        <label class="form-check-label" for="mobileFittedService">Mobile Fitted</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="fullyFittedService" name="serviceOrderType"
                            value="fullyFitted">
                        <label class="form-check-label" for="fullyFittedService">Fully Fitted</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection