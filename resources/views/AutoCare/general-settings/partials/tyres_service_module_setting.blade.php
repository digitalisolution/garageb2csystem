<form action="{{ route('settings.tyre-service.update') }}" method="POST">
    @csrf
    <h5 class="mb-3">Tyre Module Settings</h5>
    <div class="row">
        @php 
            $tyreStatus = $generalSettings->where('name','module_tyre_status')->first()->value ?? 0;
        @endphp
        <div class="col-md-12 mb-3">
            <label class="fw-bold d-flex gap-1">Tyre Status: &nbsp;&nbsp; 
                <input type="radio" name="module_tyre_status" value="1" {{ $tyreStatus == 1 ? 'checked' : '' }}> Active &nbsp;&nbsp; 
                <input type="radio" name="module_tyre_status" value="0" {{ $tyreStatus == 0 ? 'checked' : '' }}> Inactive
            </label>
        </div>
        <div class="col-12"><hr></div>
        <!-- Tyre Order Type -->
        <div class="col-md-4">
            <h5>Order Type</h5>
            @php 
                $order = json_decode($generalSettings->where('name','module_tyre_order_type')->first()->value, true) ?? [];
                $frontendBackendTypes = ['fully_fitted', 'mailorder', 'mobile_fitted'];
                $backendOnlyTypes = ['delivery', 'trade_customer_price', 'emergency'];

                $lastGroup = ''; 
            @endphp

            @foreach ($orderTypes as $type)
                @php
                    $currentType = $type->ordertype_name;
                    if (in_array($currentType, $frontendBackendTypes)) {
                        $group = 'frontend-backend';
                        $groupTitle = 'Frontend & Backend';
                    } else {
                        $group = 'backend-only';
                        $groupTitle = 'Backend Only';
                    }
                @endphp

                {{-- Print heading only ONCE per group --}}
                @if ($lastGroup !== $group)
                    <h6 class="">{{ $groupTitle }}</h6>
                    @php $lastGroup = $group; @endphp
                @endif

                {{-- Checkbox item --}}
                <label class="mb-2">
                    <input type="checkbox" name="module_tyre_order_type[]" value="{{ $currentType }}" {{ in_array($currentType, $order) ? 'checked' : '' }}> {{ ucfirst(str_replace('_', ' ', $currentType)) }}
                </label>
            @endforeach

        </div>
        <!-- Search Type -->
        <div class="col-md-4">
            <h5>Search Type</h5>
            @php 
                $search = json_decode($generalSettings->where('name','module_tyre_search_type')->first()->value, true) ?? [];
            @endphp
            <label class="mb-2">
                <input type="checkbox" name="module_tyre_search_type[]" value="vrm" {{ in_array('vrm', $search) ? 'checked' : '' }}> VRM 
            </label>
            <label class="mb-2">
                <input type="checkbox" name="module_tyre_search_type[]" value="manual" {{ in_array('manual', $search) ? 'checked' : '' }}> Manual
            </label>
        </div>
        <div class="col-12"><hr></div>
        <!-- Payment Modes -->
        <div class="col-md-12 mt-3">
            <h5>Tyre Payment Modes</h5>
            <div class="row">
                {{-- Fully Fitted Payment --}}
                @php 
                    $payFull = json_decode($generalSettings->where('name','module_tyre_payment_fully_fitted')->first()->value, true) ?? [];
                @endphp
                <div class="col-md-4">
                    <h6>Fully Fitted</h6>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_fully_fitted[]" value="online"
                        {{ in_array('online', $payFull) ? 'checked' : '' }}> Online Payment 
                    </label>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_fully_fitted[]" value="offline"
                        {{ in_array('offline', $payFull) ? 'checked' : '' }}> Offline Payment
                    </label>
                </div>

                {{-- Mobile Fitted Payment --}}
                @php 
                    $payMobile = json_decode($generalSettings->where('name','module_tyre_payment_mobile_fitted')->first()->value, true) ?? [];
                @endphp
                <div class="col-md-4">
                    <h6>Mobile Fitted</h6>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_mobile_fitted[]" value="online"
                        {{ in_array('online', $payMobile) ? 'checked' : '' }}> Online Payment </label>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_mobile_fitted[]" value="offline"
                        {{ in_array('offline', $payMobile) ? 'checked' : '' }}> Offline Payment</label>
                </div>

                {{-- Mail Order Payment --}}
                @php 
                    $payMail = json_decode($generalSettings->where('name','module_tyre_payment_mail')->first()->value, true) ?? [];
                @endphp
                <div class="col-md-4">
                    <h6>Mail Order</h6>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_mail[]" value="online"
                        {{ in_array('online', $payMail) ? 'checked' : '' }}> Online Payment </label>
                    <label class="mb-2">
                        <input type="checkbox" name="module_tyre_payment_mail[]" value="offline"
                        {{ in_array('offline', $payMail) ? 'checked' : '' }}> Offline Payment</label>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <h5 class="mb-3">Full Service Module</h5>
    <div class="row">
        @php 
            $fullServiceStatus = $generalSettings->where('name','module_fullservice_status')->first()->value ?? 0;
        @endphp        
        <div class="col-md-12 mb-3">
            <label class="fw-bold">Full Service Status:  &nbsp;&nbsp; 
                <input type="radio" name="module_fullservice_status" value="1" {{ $fullServiceStatus == 1 ? 'checked' : '' }}> Active &nbsp;&nbsp; 
                <input type="radio" name="module_fullservice_status" value="0" {{ $fullServiceStatus == 0 ? 'checked' : '' }}> Inactive
            </label>
        </div>
        <div class="col-12"><hr></div>
        <div class="col-md-4">
            <h5>Full Service Search Type</h5>
            @php 
                $serviceSearch = json_decode($generalSettings->where('name','module_fullservice_search_type')->first()->value, true) ?? [];
            @endphp
            <label class="mb-2">
            <input type="checkbox" name="module_fullservice_search_type[]" value="vrm"
                {{ in_array('vrm', $serviceSearch) ? 'checked' : '' }}> VRM </label>
            <label class="mb-2">
            <input type="checkbox" name="module_fullservice_search_type[]" value="manual"
                {{ in_array('manual', $serviceSearch) ? 'checked' : '' }}> Manual</label>
        </div>
        @php 
            $servicePayment = json_decode($generalSettings->where('name','module_fullservice_payment_mode')->first()->value, true) ?? [];
        @endphp
        <div class="col-md-4">
            <h5>Full Service Payment Mode</h5>
            <label class="mb-2">
            <input type="checkbox" name="module_fullservice_payment_mode[]" value="online"
                {{ in_array('online', $servicePayment) ? 'checked' : '' }}> Online Payment </label>
                <label class="mb-2">
            <input type="checkbox" name="module_fullservice_payment_mode[]" value="offline"
                {{ in_array('offline', $servicePayment) ? 'checked' : '' }}> Offline Payment</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
