@extends('samples')
@section('content')
        <section class="container-fluid">
            {{ Form::open(['url' => 'AutoCare/workshop/add', 'files' => 'true', 'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }}
            {{ csrf_field() }}
            {{ Form::hidden('id', isset($id) ? $id : '', []) }}
            <h5>Please Fill Up Workshop Details</h5>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            @if ($errors->any())
                                <ul class="alert alert-danger" style="list-style:none">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (session()->has('message.level'))
                                <div class="alert alert-{{ session('message.level') }} alert-dismissible"
                                    onload="javascript: Notify('You`ve got mail.', 'top-right', '5000', 'info', 'fa-envelope', true); return false;">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h6><i class="icon fa fa-check"></i>{{ ucfirst(session('message.level')) }}!</h6>
                                    {!! session('message.content') !!}
                                    @if (isset($id))
                                        <div class="text-center text-primary">
                                            <a id="openWorkshopDetail"
                                                href="{{ url('/') }}/AutoCare/workshop/view/{{ isset($id) ? $id : '' }}">Show Detail
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        setTimeout(function () {
                                            $('#openWorkshopDetail').trigger('click');
                                            var newTab = window.open(
                                                "{{ url('/') }}/AutoCare/workshop/search");
                                        }, 1000)
                                    })
                                </script>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="box-header with-border ">
                        <h6 class="box-title mb-0">Contact Detail</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class=" form-group row">
                        @if (!isset($id))
                            <div class="col-md-3">
                                <label class="control-label" for="name"> Select Customer:&emsp;</label>
                                <div class="input-group flex-nowrap">
                                    {{ Form::select('customer_id', $customerNameSelect, isset($customer_id) ? $customer_id : '', ['class' => 'form-control', 'id' => 'customer_id', 'placeholder' => 'Select Customer']) }}
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#addCustomerModal"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="invalid-feedback">
                                    {{ $errors->has('customer_id') ? $errors->first('customer_id', ':message') : '' }}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <label class="control-label" for="name"> Name/Company:<span class="text-red">*</span></label>
                            {{ Form::text('name', isset($name) ? $name : '', ['class' => 'form-control', 'id' => 'name', 'required' => 'required', 'placeholder' => ' Name/Company']) }}
                            <div class="invalid-feedback">{{ $errors->has('name') ? $errors->first('name', ':message') : '' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="mobile">Contact Number:&emsp;</label>
                            {{ Form::number('mobile', isset($mobile) ? $mobile : '', ['class' => 'form-control ', 'id' => 'mobile', 'placeholder' => ' mobile']) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('mobile') ? $errors->first('mobile', ':message') : '' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="email">Email:&emsp;</label>
                            {{ Form::email('email', isset($email) ? $email : '', ['class' => 'form-control ', 'placeholder' => 'Email']) }}
                            <div class="invalid-feedback">{{ $errors->has('email') ? $errors->first('email', ':message') : '' }}
                            </div>
                        </div>
                        @if (!isset($id))
                            <div class="col-md-3" id="registered_vehicleHS" style="display:none">
                                <label class="control-label" for="registered_vehicle">Get Vehicle By Reg Number
                                    <span class="text-red">*</span>
                                </label>
                                {{ Form::select('registered_vehicle', $registered_vehicle_select, isset($registered_vehicle) ? $registered_vehicle : '', ['class' => 'form-control', 'id' => 'registered_vehicle', 'placeholder' => 'Select Vehicle Number']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('registered_vehicle') ? $errors->first('registered_vehicle', ':message') : '' }}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3 d-none">
                            <div class="form-group">
                                <label class="control-label" for="company_name">Company Name:&emsp;</label>
                                {{ Form::text('company_name', isset($company_name) ? $company_name : '', ['class' => 'form-control', 'placeholder' => 'Company Name']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('company_name') ? $errors->first('company_name', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="reference">Reference:&emsp;</label>
                                {{ Form::text('reference', isset($reference) ? $reference : '', [
        'class' => 'form-control',
        'placeholder' => 'Reference'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('reference') ? $errors->first('reference', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_street">Address:&emsp;</label>
                                {{ Form::text('shipping_address_street', isset($address) ? $address : '', ['class' => 'form-control', 'placeholder' => 'Address']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_street') ? $errors->first('shipping_address_street', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_city">City:&emsp;</label>
                                {{ Form::text('shipping_address_city', isset($city) ? $city : '', ['class' => 'form-control', 'placeholder' => 'City']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_city') ? $errors->first('shipping_address_city', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_postcode">Postcode:&emsp;</label>
                                {{ Form::text('shipping_address_postcode', isset($zone) ? $zone : '', ['class' => 'form-control', 'placeholder' => 'Postcode', 'id' => 'shipping_address_postcode']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_postcode') ? $errors->first('shipping_address_postcode', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_county">County:&emsp;</label>
                                {{ Form::text('shipping_address_county', isset($county) ? $county : '', ['class' => 'form-control', 'placeholder' => 'County']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_county') ? $errors->first('shipping_address_county', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_country">Country:&emsp;</label>
                                {{ Form::text('shipping_address_country', isset($country) ? $country : '', ['class' => 'form-control', 'placeholder' => 'Country']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_country') ? $errors->first('shipping_address_country', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        @if (isset($id))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="status">Status:&emsp;</label>
                                    {{ Form::select('status', ['pending' => 'Pending', 'booked' => 'Booked', 'awaiting' => 'Awaiting', 'failed' => 'Failed', 'completed' => 'Completed'], isset($status) ? $status : 'pending', ['class' => 'form-control', 'required' => 'required']) }}
                                    <div class="invalid-feedback">
                                        {{ $errors->has('status') ? $errors->first('status', ':message') : '' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="status">Is Complete:</label>
                                    <div class="columns">
                                        <div class="column is-12">
                                            <div class="up-in-toggle">
                                                <input type="radio" id="switch_left" name="is_complete" checked value="0" />
                                                <label for="switch_left">No</label>
                                                <input type="radio" id="switch_right" name="is_complete" {{ isset($is_complete) && $is_complete == 1 ? 'checked' : '' }} value="1" />
                                                <label for="switch_right">Yes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        {{ $errors->has('status') ? $errors->first('status', ':status') : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        @php
    use Carbon\Carbon;
    $defaultDate = isset($workshop_date) ? $workshop_date : Carbon::now()->format('Y-m-d H:i:s');
                        @endphp
                        <div class="col-md-3">
                            <label class="control-label" for="workshop_date">Workshop Date:&emsp;</label>
                            {{ Form::text('workshop_date', $defaultDate, ['class' => 'form-control', 'id' => 'created_at', 'placeholder' => 'Workshop Date', 'data-date-format' => 'DD-MM-YYYY HH:mm:ss']) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('workshop_date') ? $errors->first('workshop_date', ':message') : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="box-header with-border">
                        <h6 class="box-title mb-0">Vehicle Details</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_reg_number">Vehicle Reg Number</label>
                                <div class="input-group">
                                    <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="ukicon" height="28">
                                    {{ Form::text('vehicle_reg_number', isset($vehicle_reg_number) ? $vehicle_reg_number : '', ['class' => 'form-control', 'placeholder' => 'Vehicle Reg Number', 'autocapitalize' => 'word', 'onkeyup' => 'this.value = this.value.toUpperCase()', 'style' => 'text-transform: uppercase', 'id' => 'vehicle_reg_number']) }}
                                    <div class="input-group-append">
                                        <button type="button" id="lookupButton" class="btn btn-primary btn-sm">Lookup</button>
                                        <button type="button" id="addVehicleButton" class="btn btn-primary btn-sm d-none"
                                            data-bs-toggle="modal" data-bs-target="#addVehicleModal"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_reg_number') ? $errors->first('vehicle_reg_number', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_make">Make:&emsp;</label>
                                {{ Form::text('vehicle_make', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_make : '', ['class' => 'form-control ', 'id' => 'vehicle_make', 'placeholder' => 'make Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_make') ? $errors->first('vehicle_make', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_model">Model:&emsp;</label>
                                {{ Form::text('vehicle_model', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_model : '', ['class' => 'form-control ', 'id' => 'vehicle_model', 'placeholder' => 'model Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_model') ? $errors->first('vehicle_model', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_first_registered">Manufacture Year:&emsp;</label>
                                {{ Form::text('vehicle_first_registered', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->first_registered : '', ['class' => 'form-control ', 'id' => 'vehicle_first_registered', 'placeholder' => 'First Registered Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_first_registered') ? $errors->first('vehicle_first_registered', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_front_tyre_size">Front Tyre Size:&emsp;</label>
                                {{ Form::text('vehicle_front_tyre_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_front_tyre_size : '', ['class' => 'form-control ', 'id' => 'vehicle_front_tyre_size', 'placeholder' => 'Front Tyre Size Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_front_tyre_size') ? $errors->first('vehicle_front_tyre_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_rear_tyre_size">Rear Tyre Size:&emsp;</label>
                                {{ Form::text('vehicle_rear_tyre_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_rear_tyre_size : '', ['class' => 'form-control ', 'id' => 'vehicle_rear_tyre_size', 'placeholder' => 'Rear Tyre Size Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_rear_tyre_size') ? $errors->first('vehicle_rear_tyre_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_vin">Vin:&emsp;</label>
                                {{ Form::text('vehicle_vin', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_vin : '', ['class' => 'form-control', 'id' => 'vehicle_vin', 'placeholder' => 'vehicle_VIN Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_vin') ? $errors->first('vehicle_vin', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_cc">Vehicle CC:&emsp;</label>
                                {{ Form::text('vehicle_cc', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_cc : '', ['class' => 'form-control ', 'id' => 'vehicle_cc', 'placeholder' => 'Vehicle CC Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_cc') ? $errors->first('vehicle_cc', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_engine_number">Engine Number:&emsp;</label>
                                {{ Form::text('vehicle_engine_number', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_engine_number : '', ['class' => 'form-control ', 'id' => 'vehicle_engine_number', 'placeholder' => 'Engine Number Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_engine_number') ? $errors->first('vehicle_engine_number', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_engine_size">Engine Size:&emsp;</label>
                                {{ Form::text('vehicle_engine_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_engine_size : '', ['class' => 'form-control ', 'id' => 'vehicle_engine_size', 'placeholder' => 'Engine Size Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_engine_size') ? $errors->first('vehicle_engine_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_axle">Axle:&emsp;</label>
                                {{ Form::text('vehicle_axle', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_axle : '', ['class' => 'form-control ', 'id' => 'vehicle_axle', 'placeholder' => 'vehicle_axle Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_axle') ? $errors->first('vehicle_axle', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_fuel_type">Fuel Type:&emsp;</label>
                                {{ Form::text('vehicle_fuel_type', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_fuel_type : '', ['class' => 'form-control ', 'id' => 'vehicle_fuel_type', 'placeholder' => 'Fuel Type Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_fuel_type') ? $errors->first('vehicle_fuel_type', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="mileage">Mileage:&emsp;</label>
                                {{ Form::text('mileage', isset($mileage) && $mileage ? $mileage : '', ['class' => 'form-control ', 'id' => 'mileage', 'placeholder' => 'Mileage Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('mileage') ? $errors->first('mileage', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_mot_expiry_date">Mot Expiry Date:&emsp;</label>
                                {{ Form::date('vehicle_mot_expiry_date', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_mot_expiry_date : null, ['class' => 'form-control', 'id' => 'vehicle_mot_expiry_date', 'placeholder' => 'Mot Expiry Date Reading']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_mot_expiry_date') ? $errors->first('vehicle_mot_expiry_date', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        @if (!isset($id))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="status">Status:&emsp;</label>
                                    {{ Form::select('status', ['pending' => 'Pending', 'booked' => 'Booked', 'awaiting' => 'Awaiting', 'failed' => 'Failed', 'completed' => 'Completed'], isset($status) ? $status : 'pending', ['class' => 'form-control', 'required' => 'required']) }}
                                    <div class="invalid-feedback">
                                        {{ $errors->has('status') ? $errors->first('status', ':message') : '' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="payment_method">Payment Method:&emsp;</label>
                                {{ Form::select('payment_method', ['pay_at_fitting_center' => 'Pay at Fitting Center', 'global_payment' => 'Global Payment', 'dojo' => 'Dojo'], isset($payment_method) ? $payment_method : 'pay_at_fitting_center', ['class' => 'form-control', 'required' => 'required']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('payment_method') ? $errors->first('payment_method', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="payment_status">Payment Status:&emsp;</label>
                                {{ Form::select('payment_status', ['1' => 'Paid', '0' => 'Unpaid', '3' => 'Partially'], isset($payment_status) ? $payment_status : '0', ['class' => 'form-control', 'required' => 'required']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('payment_status') ? $errors->first('payment_status', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="fitting_type">Fitting Type:&emsp;</label>
                                <select id="fitting_type" name="fitting_type" class="form-control" required>
                                </select>
                                <div class="invalid-feedback">
                                    {{ $errors->has('fitting_type') ? $errors->first('fitting_type', ':message') : '' }}
                                </div>
                            </div>
                        </div>

                       <!-- Garage dropdown visible for ALL fitting types -->
                    <div class="col-md-3" id="garageSelectionSection">
                        <div class="form-group">
                            <label class="control-label" for="garages_data_name">Garage Fitting Name:&emsp;<span
                                    class="text-red">*</span></label>
                            <select id="garages_data_name" name="garage_id" class="form-control" required>
                                <option value="">-- Select Garage --</option>
                            </select>
                            <div class="invalid-feedback">
                                {{ $errors->has('garage_id') ? $errors->first('garage_id', ':message') : '' }}
                            </div>
                        </div>
                    </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="due_in">Due In <span class="text-red">*</span></label>
                                @php
    $dueInValue = null;
    if (isset($id) && $due_in) {
        $dueInValue = \Carbon\Carbon::parse($due_in)->format('Y-m-d\TH:i');
    } elseif (request('due_in')) {
        $dueInValue = \Carbon\Carbon::parse(request('due_in'))->format('Y-m-d\TH:i');
    } else {
        $dueInValue = old('due_in', \Carbon\Carbon::now('Europe/London')->format('Y-m-d\TH:i'));
    }
                                @endphp

                                {{ Form::input('datetime-local', 'due_in', $dueInValue, ['class' => 'form-control ' . ($errors->has('due_in') ? 'is-invalid' : ''), 'id' => 'due_in', 'required' => true, 'placeholder' => 'Due In',]) }}
                                @error('due_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="due_out">Due Out <span class="text-red">*</span></label>
                                @php
    $dueOutValue = null;
    if (isset($id) && $due_out) {
        $dueOutValue = \Carbon\Carbon::parse($due_out)->format('Y-m-d\TH:i');
    } elseif (request('due_out')) {
        $dueOutValue = \Carbon\Carbon::parse(request('due_out'))->format('Y-m-d\TH:i');
    } else {
        $dueOutValue = old('due_out', \Carbon\Carbon::now('Europe/London')->addHours(2)->format('Y-m-d\TH:i'));
    }
                                @endphp

                                {{ Form::input('datetime-local', 'due_out', $dueOutValue, ['class' => 'form-control ' . ($errors->has('due_out') ? 'is-invalid' : ''), 'id' => 'due_out', 'required' => true, 'placeholder' => 'Due Out',]) }}
                                @error('due_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="advisor">Notes:&emsp;</label>
                                {{ Form::textarea('notes', isset($notes) ? $notes : '', ['class' => 'form-control ', 'placeholder' => 'Notes', 'style' => 'height:50px']) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('notes') ? $errors->first('notes', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row text-center mb-4 justify-content-center">
                <div class="col-2"><button id="addTyreButton" class="btn btn-primary btn-block" type="button">Add Tyre</button>
                </div>
                <div class="col-2"><button id="addServiceButton" class="btn btn-primary btn-block" type="button">Add
                        Service</button></div>
                <div class="col-2"><button id="addPartButton" class="btn btn-primary btn-block" type="button">Add Part</button>
                </div>
                <div class="col-2"><button id="addConsumableButton" class="btn btn-primary btn-block" type="button">Add
                        Consumable</button></div>
                <div class="col-2"><button id="addLabourButton" class="btn btn-primary btn-block" type="button">Add
                        Labour</button></div>
            </div>
            <!-- tyre section start -->
            <div class="card">
                <h6 class="card-header">Tyres</h6>
                <!-- Include Modal for Tyre Selection -->
                @include('AutoCare/workshop/tyre-modal')
                @include('AutoCare/workshop/service-modal')
                @include('AutoCare/workshop/part-modal')
                @include('AutoCare/workshop/consumable-modal')
                @include('AutoCare/workshop/labour-modal')
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <th style="white-space: nowrap">Item &emsp;&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Description &emsp;&emsp;</th>
                            <th style="white-space: nowrap">Quantity&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Cost Price&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Rate&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Vat &emsp;&emsp;</th>
                            <th style="white-space: nowrap">Amount&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Action</th>
                        </thead>
                        <tbody id="tBodyForProductTable">
                            @if (isset($workshopTyreData))
                                @php
        $incrementedId = 0;
                                @endphp
                                @foreach ($workshopTyreData as $key => $value)
                                    @php
            $quantity = $value->quantity ?? 1;
            $tax_class_id = $value->tax_class_id ?? 0;
            $vatRate = ($tax_class_id == 9) ? 0.2 : 0;
            $price = $value->cost_price ?? 0;
            $rate = $value->margin_rate ?? 0;
            $subtotal = $rate * $quantity;
            $vatAmount = $subtotal * $vatRate;
            $totalAmount = $subtotal + $vatAmount;
                                    @endphp

                                    <tr id="AddRowForProduct{{ $incrementedId }}">
                                        <input type="hidden" name="item_id[]" value="{{ $value->id }}" required>
                                        <input type="hidden" name="product_id[]" value="{{ $value->product_id }}" required>
                                        <input type="hidden" name="tyre_ean[]" value="{{ $value->product_ean }}">
                                        <input type="hidden" name="tyre_sku[]" value="{{ $value->product_sku }}">
                                        <input type="hidden" name="tyre_supplier_name[]" value="{{ $value->supplier }}">
                                        <input type="hidden" name="product_type" value="tyre">

                                        <input type="hidden" name="tyre_description[]" value="{{ $value->description }}" required>

                                        <td>{{ $value->tyre_type }} {{ $value->tyre_brand_name }}
                                            {{ $value->description }}
                                        </td>
                                        <td>
                                            <strong>{{ $value->product_ean }}</strong>
                                            {{ $value->description }}
                                        </td>
                                        <td>
                                            <input type="number" name="tyre_quantity[]" value="{{ $quantity }}" min="1"
                                                class="form-control quantity" required>
                                            <small class="stock-error text-danger"></small>
                                        </td>
                                        <td>
                                            <input type="number" name="tyre_cost_price[]" value="{{ number_format($price, 2) }}"
                                                class="form-control cost-price" step="0.01" required>
                                        </td>
                                        <td>
                                            <input type="number" name="tyre_margin_rate[]" value="{{ number_format($rate, 2) }}"
                                                class="form-control price" step="0.01">
                                        </td>
                                        <td>
                                            <select name="tyre_vat[]" class="form-control vat-type">
                                                <option value="9" {{ $tax_class_id == 9 ? 'selected' : '' }}>20% VAT
                                                </option>
                                                <option value="0" {{ $tax_class_id == 0 ? 'selected' : '' }}>No VAT
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="tyre_amount[]" step="0.01"
                                                value="{{ number_format($totalAmount, 2) }}" class="form-control total-amount">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm removeRow">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @php
            $incrementedId++;
                                    @endphp
                                @endforeach
                            @else
                                <tr class="estimatedCost">
                                    {{-- <td colspan="8">Please Add Tyre</td> --}}
                                </tr>
                            @endif
                        </tbody>
                        @if (isset($id))
                            <tfoot>
                                <tr>

                                </tr>
                            </tfoot>
                        @else
                            <tr>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="card">
                <h6 class="card-header">Services</h6>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" id="selectedServicesTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Service Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>VAT</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tBodyForProductTable">
                            @if (isset($workshopServiceData))

                                @php
        $incrementedId = 0;
                                @endphp
                                @foreach ($workshopServiceData as $key => $value)
                                    @php
            $quantity = $value->service_quantity ?? 1;
            $tax_class_id = $value->tax_class_id ?? 0;
            $vatRate = $tax_class_id == 9 ? 0.2 : 0;
            $price = $value->service_price ?? 0;
            $subtotal = $price * $quantity;
            $vatAmount = $subtotal * $vatRate;
            $totalAmount = $subtotal + $vatAmount;
                                    @endphp

                                    <tr id="AddRowForProduct{{ $incrementedId }}">
                                        <input type="hidden" name="service_id[]" value="{{ $value->service_id }}">

                                        <td>
                                            <input type="hidden" name="service_name[]" value="{{ $value->service_name }}">
                                            {{ $value->service_name }}
                                        </td>
                                        <td>
                                            <input type="number" name="service_quantity[]" class="form-control quantity"
                                                value="{{ $quantity }}" min="1" data-price="{{ $price }}">
                                        </td>
                                        <td>
                                            <input type="number" name="service_price[]" class="form-control price" value="{{ $price }}"
                                                step="0.01">
                                        </td>
                                        <td>
                                            <input type="hidden" name="service_vat[]" value="{{ $tax_class_id }}">
                                            <select name="vat_rate[]" class="form-control vat-rate">
                                                <option value="0" {{ $tax_class_id == 0 ? 'selected' : '' }}>0% VAT
                                                </option>
                                                <option value="0.2" {{ $tax_class_id == 9 ? 'selected' : '' }}>20% VAT
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="service_total[]" class="form-control service-total"
                                                value="{{ number_format($totalAmount, 2) }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-service">Remove</button>
                                        </td>
                                    </tr>
                                    @php
            $incrementedId++;
                                    @endphp
                                @endforeach
                            @else
                                <tr class="estimatedCost">
                                    {{-- <td colspan="6">Please Add a Service</td> --}}
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if($moduleConsumableEnabled)
                <div class="card">
                    <h6 class="card-header">Consumable</h6>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm" id="selectedConsumablesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Consumable Name</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>VAT</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tBodyForProductTable">
                                @if (isset($workshopConsumableData))

                                    @php
            $incrementedId = 0;
                                    @endphp
                                    @foreach ($workshopConsumableData as $key => $value)
                                        @php
                $quantity = $value->consumable_quantity ?? 1;
                $tax_class_id = $value->tax_class_id ?? 0;
                $vatRate = $tax_class_id == 9 ? 0.2 : 0;
                $price = $value->consumable_price ?? 0;
                $subtotal = $price * $quantity;
                $vatAmount = $subtotal * $vatRate;
                $totalAmount = $subtotal + $vatAmount;
                                        @endphp

                                        <tr id="AddRowForProduct{{ $incrementedId }}">
                                            <input type="hidden" name="consumable_id[]" value="{{ $value->consumable_id }}">

                                            <td>
                                                <input type="hidden" name="consumable_name[]" value="{{ $value->consumable_name }}">
                                                {{ $value->consumable_name }}
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="consumable_content[]"
                                                    value="{{ $value->consumable_content }}">{{ $value->consumable_content }}</textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="consumable_quantity[]" class="form-control quantity"
                                                    value="{{ $quantity }}" min="1" data-price="{{ $price }}">
                                            </td>
                                            <td>
                                                <input type="number" name="consumable_price[]" class="form-control price"
                                                    value="{{ $price }}" step="0.01">
                                            </td>
                                            <td>
                                                <input type="hidden" name="consumable_vat[]" value="{{ $tax_class_id }}">
                                                <select name="vat_rate[]" class="form-control vat-rate">
                                                    <option value="0" {{ $tax_class_id == 0 ? 'selected' : '' }}>0% VAT
                                                    </option>
                                                    <option value="0.2" {{ $tax_class_id == 9 ? 'selected' : '' }}>20% VAT
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="consumable_total[]"
                                                    class="form-control consumable-total" value="{{ number_format($totalAmount, 2) }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-consumable">Remove</button>
                                            </td>
                                        </tr>
                                        @php
                $incrementedId++;
                                        @endphp
                                    @endforeach
                                @else
                                    <tr class="estimatedCost">
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if($modulePartEnabled)
                <div class="card">
                    <h6 class="card-header">Part</h6>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm" id="selectedPartsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Part Name</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>VAT</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tBodyForProductTable">
                                @if (isset($workshopPartData))

                                    @php
            $incrementedId = 0;
                                    @endphp
                                    @foreach ($workshopPartData as $key => $value)
                                        @php
                $quantity = $value->part_quantity ?? 1;
                $tax_class_id = $value->tax_class_id ?? 0;
                $vatRate = $tax_class_id == 9 ? 0.2 : 0;
                $price = $value->part_price ?? 0;
                $subtotal = $price * $quantity;
                $vatAmount = $subtotal * $vatRate;
                $totalAmount = $subtotal + $vatAmount;
                                        @endphp

                                        <tr id="AddRowForProduct{{ $incrementedId }}">
                                            <input type="hidden" name="part_id[]" value="{{ $value->part_id }}">

                                            <td>
                                                <input type="hidden" name="part_name[]" value="{{ $value->part_name }}">
                                                {{ $value->part_name }}
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="part_content[]"
                                                    value="{{ $value->part_content }}">{{ $value->part_content }}</textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="part_quantity[]" class="form-control quantity"
                                                    value="{{ $quantity }}" min="1" data-price="{{ $price }}">
                                            </td>
                                            <td>
                                                <input type="number" name="part_price[]" class="form-control price" value="{{ $price }}"
                                                    step="0.01">
                                            </td>
                                            <td>
                                                <input type="hidden" name="part_vat[]" value="{{ $tax_class_id }}">
                                                <select name="vat_rate[]" class="form-control vat-rate">
                                                    <option value="0" {{ $tax_class_id == 0 ? 'selected' : '' }}>0% VAT
                                                    </option>
                                                    <option value="0.2" {{ $tax_class_id == 9 ? 'selected' : '' }}>20% VAT
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="part_total[]" class="form-control part-total"
                                                    value="{{ number_format($totalAmount, 2) }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-consumable">Remove</button>
                                            </td>
                                        </tr>

                                        @php
                $incrementedId++;
                                        @endphp
                                    @endforeach
                                @else
                                    <tr class="estimatedCost">
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if($moduleLabourEnabled)
                <div class="card">
                    <h6 class="card-header">Labour</h6>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm" id="selectedLaboursTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Labour Name</th>
                                    <th>Description</th>
                                    <th>Hours</th>
                                    <th>Price</th>
                                    <th>VAT</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tBodyForProductTable">
                                @if (isset($WorkshopLabourData))
                                    @php
            $incrementedId = 0;
                                    @endphp
                                    @foreach ($WorkshopLabourData as $key => $value)
                                        @php
                $quantity = $value->labour_quantity ?? 1;
                $tax_class_id = $value->tax_class_id ?? 0;
                $vatRate = $tax_class_id == 9 ? 0.2 : 0;
                $price = $value->labour_price ?? 0;
                $subtotal = $price * $quantity;
                $vatAmount = $subtotal * $vatRate;
                $totalAmount = $subtotal + $vatAmount;
                                        @endphp

                                        <tr id="AddRowForProduct{{ $incrementedId }}">
                                            <input type="hidden" name="labour_id[]" value="{{ $value->labour_id }}">

                                            <td>
                                                <input type="hidden" name="labour_name[]" value="{{ $value->labour_name }}">
                                                {{ $value->labour_name }}
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="labour_content[]"
                                                    value="{{ $value->labour_content }}">{{ $value->labour_content }}</textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="labour_quantity[]" class="form-control quantity"
                                                    value="{{ $quantity }}" min="1" data-price="{{ $price }}">
                                            </td>
                                            <td>
                                                <input type="number" name="labour_price[]" class="form-control price" value="{{ $price }}"
                                                    step="0.01">
                                            </td>
                                            <td>
                                                <input type="hidden" name="labour_vat[]" value="{{ $tax_class_id }}">
                                                <select name="vat_rate[]" class="form-control vat-rate">
                                                    <option value="0" {{ $tax_class_id == 0 ? 'selected' : '' }}>0% VAT
                                                    </option>
                                                    <option value="0.2" {{ $tax_class_id == 9 ? 'selected' : '' }}>20% VAT
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="labour_total[]" class="form-control labour-total"
                                                    value="{{ number_format($totalAmount, 2) }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger remove-labour">Remove</button>
                                            </td>
                                        </tr>
                                        @php
                $incrementedId++;
                                        @endphp
                                    @endforeach
                                @else
                                    <tr class="estimatedCost">
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="card-footer text-center">
                <div class="row">
                    <div class="col-2">
                        Total Tyre Price : <b id="total_Product_amount"></b>
                        <input type="hidden" name="total_tyre_price" id="total_tyre_price_input">
                    </div>
                    <div class="col-2">Total Service Price : <b id="total_Service_amount"></b>
                        <input type="hidden" name="total_service_price" id="total_service_price_input">
                    </div>
                    <div class="col-2">Total Consumable Price : <b id="total_consumable_amount"></b>
                        <input type="hidden" name="total_consumable_price" id="total_consumable_price_input">
                    </div>
                    <div class="col-2">Total Part Price : <b id="total_part_amount"></b>
                        <input type="hidden" name="total_part_price" id="total_part_price_input">
                    </div>
                    <div class="col-2">Total Labour Price : <b id="total_labour_amount"></b>
                        <input type="hidden" name="total_labour_price" id="total_labour_price_input">
                    </div>
                    <div class="col-2" id="calloutChargesSection" style="display: none;">
                        <div class="text-callout-red">
                            Callout Charges: <b id="calloutCharges"></b><br>
                            <input type="hidden" name="callout_charges" id="callout_charges_input">
                            <input type="hidden" name="callout_vat" id="callout_vat_input">
                            <input type="hidden" name="callout_postcode" id="callout_postcode_input">
                            <input type="hidden" name="total_callout" id="total_callout_input">
                        </div>
                    </div>
                    <div class="col-2" id="garageFittingChargesSection" style="display: none;">
                        <div class="text-callout-red">
                            Garage Fitting Charges: <b id="garageFittingCharges"></b><br>
                            <input type="hidden" name="garage_fitting_charges" id="garage_fitting_charges_input">
                            <input type="hidden" name="garage_fitting_vat" id="garage_fitting_vat_input">
                            <input type="hidden" name="total_garage_fitting" id="total_garage_fitting_input">
                        </div>
                    </div>
                    <div class="col-2">
                        Grand Total : <b id="total_grand_amount"></b>
                        <input type="hidden" name="grand_total" id="grand_total_input">
                    </div>
                </div>
            </div>
            <!-- tyre section end -->
            @php
    $incrimentedId_es = 0;
            @endphp
            @if(!isset($id))
            @endif
            <div class="col-md-12 text-center mt-3 mb-3">
                <button type="submit" class="btn btn-sm btn-primary" name="save_only" id="saveOnlyBtn">
                    <i class="fa fa-dot-circle-o"></i> {{ isset($id) ? 'Update' : 'Add' }}
                </button>
                <button type="submit" class="btn btn-sm btn-success" name="save_and_sync_invoice" id="saveAndSyncBtn">
                    <i class="fa fa-file-invoice"></i> {{ isset($id) ? 'Update & Sync Invoice' : 'Add & Sync Invoice' }}
                </button>
                <button type="reset" class="btn btn-sm btn-danger" name="reset">
                    <i class="fa fa-ban"></i> Reset
                </button>
            </div>
            {{ Form::close() }}
        </section>
        <!-- Modal for postcode -->
        <div class="modal fade" id="postcodeModal" tabindex="-1" role="dialog" aria-labelledby="postcodeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content mobile_popup_content">
                    <!-- Close Button 
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->

                    <div class="mobile_popup text-center">
                        <img src="frontend/themes/default/img/icon-img/mobile-van.png" alt="mobile van">
                        <span class="bottom_arrow"></span>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <h5>Enter Your Shipping Postcode for Mobile Fitting</h5>

                        <!-- Postcode Form -->
                        <form id="postcodeForm" action="/submit-postcode" method="POST">
                            <div class="col-lg-8 m-auto text-center">
                                <input type="text" class="form-control mb-2" name="postcode" id="postcode" maxlength="8"
                                    placeholder="ENTER POSTCODE" required>
                                <button type="submit" class="btn btn-primary btn-block">Submit</button>
                            </div>
                        </form>

                        <div class="mt-2">
                            <span id="postcodeErrorMsg" class="text-danger"></span>
                        </div>
                        <div id="resultMessage" class="mt-3"></div>
                        <div class="mt-3" id="actionButtons">
                            <button id="continueButton" class="btn btn-success me-2" style="display: none;">Continue</button>
                            <button id="changePostcodeButton" class="btn btn-secondary" style="display: none;">Change
                                Postcode</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!isset($id))
            @include('AutoCare/workshop/modal/add_customer')
        @endif
        @include('AutoCare/workshop/modal/add_vehicle')
        @include('AutoCare/workshop/modal/vrm_customer')
        @include('AutoCare/workshop/modal/add_tyre')
        @include('AutoCare/workshop/modal/add_part')
        @include('AutoCare/workshop/modal/add_consumable')
        @include('AutoCare/workshop/modal/add_labour')

       @php
    $sourceData = null;
    if (isset($workshopTyreData) && $workshopTyreData->isNotEmpty()) {
        $sourceData = $workshopTyreData->first();
    }

    $fitting_type = optional($sourceData)->fitting_type ?? 'fully_fitted';
    $shipping_postcode = optional($sourceData)->shipping_postcode ?? '';
    $callout_charges = optional($sourceData)->shipping_price ?? 0;
    $callout_vat = optional($sourceData)->shipping_tax_id ?? 0;
    $total_callout = (optional($sourceData)->shipping_price ?? 0) * 1.2;
    $garage_fitting_charges = optional($sourceData)->garage_fitting_charges ?? 0;
    $garage_fitting_vat = optional($sourceData)->garage_vat_class ?? 0;
    $total_garage_fitting = (optional($sourceData)->garage_fitting_price ?? 0) * 1.2;
    $garage_id = optional($sourceData)->garage_id ?? '';
    @endphp

        <script>
            document.getElementById('due_in').addEventListener('change', function () {
                const dueIn = this.value;
                if (dueIn) {
                    const dueOutInput = document.getElementById('due_out');
                    const dueInDate = new Date(dueIn);
                    dueInDate.setHours(dueInDate.getHours() + 2);
                    dueOutInput.value = dueInDate.toISOString().slice(0, 16);
                }
            });
        </script>
        <script>
            function fetchGaragesList() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch('/ajax/get-garages-list', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.garages) {
            const garageDropdown = document.getElementById('garages_data_name');
            garageDropdown.innerHTML = '<option value="">-- Select Garage --</option>';
            
            let defaultGarageFound = false;
            let defaultGarageId = null;
            
            data.garages.forEach(garage => {
                const option = document.createElement('option');
                option.value = garage.id;
                option.text = garage.garage_name;
                
                // Check if this is "Tyre Lab" garage (case-insensitive)
                if (garage.garage_name.toLowerCase().includes('tyre lab')) {
                    option.selected = true;
                    defaultGarageFound = true;
                    defaultGarageId = garage.id;
                }
                
                // Also check for existing garage_id (edit mode)
                if ('{{ isset($garage_id) ? $garage_id : "" }}' == garage.id) {
                    option.selected = true;
                }
                
                garageDropdown.appendChild(option);
            });
            
            // Fetch details for default garage or existing garage
            if (defaultGarageId && !'{{ isset($garage_id) ? $garage_id : "" }}') {
                // New workshop - use Tyre Lab as default
                fetchGarageDetails(defaultGarageId);
            } else if (garageDropdown.value) {
                // Edit workshop - use existing garage
                fetchGarageDetails(garageDropdown.value);
            } else {
                clearGarageCharges();
            }
        }
    })
    .catch(error => console.error('Error fetching garages:', error));
}
            function fetchGarageDetails(garageId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('/ajax/get-garage-details', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ garage_id: garageId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.garageFittingResponseData = {
                                fitting_price: parseFloat(data.fitting_price),
                                includes_vat: parseInt(data.vat_class),
                                total_price: 0
                            };
                            updateTotals();
                        }
                    })
                    .catch(error => console.error('Error fetching garage details:', error));
            }

            function clearGarageCharges() {
                window.garageFittingResponseData = null;
                $('#garageFittingCharges').text('£0.00');
                $('#garage_fitting_charges_input').val(0);
                $('#garage_fitting_vat_input').val(0);
                $('#total_garage_fitting_input').val(0);
                updateTotals();
            }

document.addEventListener('DOMContentLoaded', function () {
    const fittingTypeDropdown = document.getElementById('fitting_type');
    const garageSelectionSection = document.getElementById('garageSelectionSection');
    const garageDropdown = document.getElementById('garages_data_name');
    const garageFittingChargesSection = document.getElementById('garageFittingChargesSection');
    const postcodeModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
        backdrop: true,
        keyboard: true,
    });

    // Function to toggle garage required attribute
    function toggleGarageRequired(isRequired) {
        if (garageDropdown) {
            if (isRequired) {
                garageDropdown.setAttribute('required', 'required');
            } else {
                garageDropdown.removeAttribute('required');
                garageDropdown.value = '';
            }
        }
    }

    fittingTypeDropdown.addEventListener('change', function () {
        if (['mobile_fitted','mailorder'].includes(this.value)) {
            $('#calloutChargesSection').show();
            $('#garageFittingChargesSection').hide();  // Hide charges for mobile/mailorder
            toggleGarageRequired(true);  // Garage still required
            
            // Load garages if not loaded
            if (garageDropdown && garageDropdown.options.length <= 1) {
                fetchGaragesList();
            }
            
            postcodeModal.show();
        } else if (this.value === 'fully_fitted') {
            $('#calloutChargesSection').hide();
            $('#garageFittingChargesSection').show();  // Show charges for fully_fitted
            toggleGarageRequired(true);  // Garage still required
            
            // Load garages if not loaded
            if (garageDropdown && garageDropdown.options.length <= 1) {
                fetchGaragesList();
            }
            
            // Fetch garage details for charges only when fully_fitted
            if (garageDropdown.value) {
                fetchGarageDetails(garageDropdown.value);
            } else {
                clearGarageCharges();
            }
        } else {
            $('#calloutChargesSection').hide();
            $('#garageFittingChargesSection').hide();
            toggleGarageRequired(true);  // Garage still required
            clearGarageCharges();
        }
    });

    // Garage Selection Change Handler
    if (garageDropdown) {
        garageDropdown.addEventListener('change', function () {
            // Only fetch charges when fitting type is fully_fitted
            if ($('#fitting_type').val() === 'fully_fitted') {
                if (this.value) {
                    fetchGarageDetails(this.value);
                } else {
                    clearGarageCharges();
                }
            }
            // For other fitting types, garage is selected but no charges fetched
        });
    }

    // Load garages on page load
    if (garageDropdown && garageDropdown.options.length <= 1) {
        fetchGaragesList();
    }


                document.getElementById('postcodeForm').addEventListener('submit', function (event) {
                    event.preventDefault();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        return;
                    }
                    const postcodeInput = document.getElementById('postcode');
                    const errorMsg = document.getElementById('postcodeErrorMsg');
                    const resultDiv = document.getElementById('resultMessage');
                    const continueButton = document.getElementById('continueButton');
                    const changePostcodeButton = document.getElementById('changePostcodeButton');

                    errorMsg.textContent = '';
                    resultDiv.innerHTML = '';

                    fetch('/calculate-shipping', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            postcode: postcodeInput.value
                        }),
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'An unexpected error occurred.');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                errorMsg.textContent = data.error;
                            } else {
                                window.postcodeResponseData = data;
                                resultDiv.innerHTML = `
                                                                Postcode: ${data.postcode}<br>
                                                                Distance: ${data.distance_in_miles} Miles<br>
                                                                Total Price: £${data.total_price}
                                                            `;

                                continueButton.style.display = 'inline-block';
                                changePostcodeButton.style.display = 'inline-block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            errorMsg.textContent = error.message || 'An unexpected error occurred.';
                        });
                });

                document.getElementById('changePostcodeButton').addEventListener('click', function () {
                    document.getElementById('postcode').value = '';
                    document.getElementById('resultMessage').innerHTML = '';
                    document.getElementById('continueButton').style.display = 'none';
                    document.getElementById('changePostcodeButton').style.display = 'none';
                });

                document.getElementById('continueButton').addEventListener('click', function () {
                    const data = window.postcodeResponseData;

                    document.getElementById('shipping_address_postcode').value = data.postcode;

                    const calloutChargesSection = document.getElementById('calloutChargesSection');
                    calloutChargesSection.style.display = 'block';
                    document.getElementById('calloutCharges').textContent = `£${data.total_price.toFixed(2)}`;
                    document.getElementById('callout_charges_input').value = data.ship_price;
                    document.getElementById('callout_vat_input').value = data.includes_vat;
                    document.getElementById('callout_postcode_input').value = data.postcode;
                    document.getElementById('total_callout_input').value = data.total_price;

                    fetch('/store-postcode-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify(data),
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'Failed to save postcode data.');
                                });
                            }
                            return response.json();
                        })
                        .then(result => {
                            if (result.success) {
                                postcodeModal.hide();
                                updateTotals();
                            } else {
                                alert('Failed to save postcode data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert(error.message || 'An unexpected error occurred while saving postcode data.');
                        });
                });
            });
        </script>
       @if(in_array($fitting_type, ['mobile_fitted','mailorder']))
<script>
$(document).ready(function () {
    $('#fitting_type').val('{{ $fitting_type }}');
    
    // Show garage section for all types
    $('#garageSelectionSection').show();
    
    if (['mobile_fitted','mailorder'].includes('{{ $fitting_type }}')) {
        $('#calloutChargesSection').show();
        $('#garageFittingChargesSection').hide();  // Hide charges
        
        // Load garage list
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch('/ajax/get-garages-list', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.garages) {
                const garageDropdown = document.getElementById('garages_data_name');
                garageDropdown.innerHTML = '<option value="">-- Select Garage --</option>';
                data.garages.forEach(garage => {
                    const option = document.createElement('option');
                    option.value = garage.id;
                    option.text = garage.garage_name;
                    if ('{{ isset($garage_id) ? $garage_id : "" }}' == garage.id) {
                        option.selected = true;
                    }
                    garageDropdown.appendChild(option);
                });
            }
        });
        
        // Set callout charges
        $('#calloutCharges').text('£{{ number_format($total_callout, 2) }}');
        $('#callout_charges_input').val('{{ $callout_charges }}');
        $('#callout_vat_input').val('{{ $callout_vat }}');
        $('#callout_postcode_input').val('{{ $shipping_postcode }}');
        $('#total_callout_input').val('{{ $total_callout }}');
        window.postcodeResponseData = {
            postcode: '{{ $shipping_postcode }}',
            ship_price: {{ $callout_charges }},
            includes_vat: {{ $callout_vat }},
            total_price: {{ $total_callout }}
        };
        $('#shipping_address_postcode').val('{{ $shipping_postcode }}');
    }
    
    updateTotals();
});
</script>
@elseif($fitting_type === 'fully_fitted')
<script>
$(document).ready(function () {
    $('#fitting_type').val('{{ $fitting_type }}');
    
    // Show garage section and charges
    $('#garageSelectionSection').show();
    $('#garageFittingChargesSection').show();
    
    // Load garage list
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch('/ajax/get-garages-list', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.garages) {
            const garageDropdown = document.getElementById('garages_data_name');
            garageDropdown.innerHTML = '<option value="">-- Select Garage --</option>';
            data.garages.forEach(garage => {
                const option = document.createElement('option');
                option.value = garage.id;
                option.text = garage.garage_name;
                if ('{{ isset($garage_id) ? $garage_id : "" }}' == garage.id) {
                    option.selected = true;
                }
                garageDropdown.appendChild(option);
            });
            
            // Fetch garage details for charges
            if (garageDropdown.value) {
                fetchGarageDetails(garageDropdown.value);
            }
        }
    });
    
    updateTotals();
});
</script>
@endif
        <style>
            .text-red {
                color: red;
                font-size: 15px;
            }

            .text-callout-red {
                color: red;
            }
        </style>
        <script>
            $(document).ready(function () {
                const formSelector = 'form[action="{{ url('AutoCare/workshop/add') }}"]';
                $(formSelector).on('submit', function (e) {
                    const clickedButton = document.activeElement;
                    if (clickedButton && (clickedButton.id === 'saveOnlyBtn' || clickedButton.id === 'saveAndSyncBtn' || clickedButton.name === 'save_only' || clickedButton.name === 'save_and_sync_invoice')) {
                        const buttonName = $(clickedButton).attr('name');
                        const buttonValue = $(clickedButton).val();
                        $(this).find('input[name="' + buttonName + '"]').remove();
                        $('<input>').attr({
                            type: 'hidden',
                            name: buttonName,
                            value: buttonValue
                        }).appendTo(this);

                        // Now safe to disable buttons
                        $(clickedButton).prop('disabled', true);
                        const originalHtml = $(clickedButton).html();
                        $(clickedButton).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                        $(clickedButton).data('original-html', originalHtml);

                        const otherButtonSelector = clickedButton.id === 'saveOnlyBtn' ? '#saveAndSyncBtn' : '#saveOnlyBtn';
                        $(otherButtonSelector).prop('disabled', true);
                    }
                });

            });
        </script>
        <script>
            const CURRENT_WORKSHOP_ID = {{ isset($id) ? json_encode($id) : 'null' }};
        </script>
        <script>
            let allRowsValid = true;
            async function validateStock(row) {
                if (!(row instanceof jQuery)) {
                    row = $(row);
                }
                try {
                    const ean = row.find('input[name="tyre_ean[]"]').val() || '';
                    const supplier = row.find('input[name="tyre_supplier_name[]"]').val() || '';
                    const workshopId = CURRENT_WORKSHOP_ID;

                    if (!ean || !supplier) {
                        row.find('.stock-error').text('EAN & Supplier required for stock check.');
                        row.addClass('error-row');
                        allRowsValid = false;
                        checkFormValidity();
                        return false;
                    }

                    let url = `/validate-tyre-stock-by-ean/${encodeURIComponent(ean)}/${encodeURIComponent(supplier)}`;
                    if (workshopId) {
                        url += `?workshop_id=${workshopId}`;
                    }

                    const response = await fetch(url);
                    const data = await response.json();

                    const qtyInput = row.find('input[name="tyre_quantity[]"]');
                    const currentQty = parseInt(qtyInput.val()) || 1;
                    const available = data.available || 0;

                    if (!data.success || currentQty > available) {
                        row.find('.stock-error').text(`Only ${available} in stock!`);
                        row.addClass('error-row');
                        qtyInput.prop('disabled', true);
                        qtyInput.attr('max', available);
                        allRowsValid = false;
                    } else {
                        row.find('.stock-error').text('');
                        row.removeClass('error-row');
                        qtyInput.attr('max', available);
                        qtyInput.prop('disabled', false);
                    }

                    checkFormValidity();
                    return data.success && currentQty <= available;
                } catch (error) {
                    console.error('Stock validation error:', error);
                    row.find('.stock-error').text('Validation failed.');
                    row.addClass('error-row');
                    allRowsValid = false;
                    checkFormValidity();
                    return false;
                }
            }

            function checkFormValidity() {
                allRowsValid = $('#tBodyForProductTable .error-row').length === 0;
                const submitButtons = $('#saveOnlyBtn, #saveAndSyncBtn');
                if (allRowsValid) {
                    submitButtons.prop('disabled', false).removeClass('btn-secondary');
                } else {
                    submitButtons.prop('disabled', true).addClass('btn-secondary');
                }
            }

        </script>
        <script type="text/javascript">
            $(window).on('load', function () {
                var today = new Date();
                $('#created_at').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                    endDate: '+0d',
                });

            })
            $(document).ready(function () {
                $('[name^=product_price_es]').removeAttr("min");
                $('[name^=product_quantity_es]').removeAttr("max");
                var TotalProduct = 0;
                var TotalService = 0;
                var total_consumable_amount = 0;
                var total_part_amount = 0;
                var total_labour_amount = 0;
                var total_grand_amount = 0;
                $("[name^='product_price']")
                    .map(function () {
                        if (!isNaN(parseFloat($(this).val()))) {
                            TotalProduct += parseFloat($(this).val()) * parseFloat($(this).parent().parent().find(
                                '[name^=product_quantity]').val());
                        }
                        return parseFloat($(this).val());
                    }).get();

                $("[name^='service_price']")
                    .map(function () {
                        if (!isNaN(parseFloat($(this).val()))) {
                            TotalService += parseFloat($(this).val()) * parseFloat($(this).parent().parent()
                                .find('[name^=service_quantity]').val());
                        }
                        return parseFloat($(this).val());

                    }).get();
                $("[name^='consumable_price']")
                    .map(function () {
                        if (!isNaN(parseFloat($(this).val()))) {
                            total_consumable_amount += parseFloat($(this).val()) * parseFloat($(this).parent().parent()
                                .find('[name^=consumable_quantity]').val());
                        }
                        return parseFloat($(this).val());

                    }).get();
                $("[name^='part_price']")
                    .map(function () {
                        if (!isNaN(parseFloat($(this).val()))) {
                            total_part_amount += parseFloat($(this).val()) * parseFloat($(this).parent().parent()
                                .find('[name^=part_quantity]').val());
                        }
                        return parseFloat($(this).val());

                    }).get();
                $("[name^='labour_price']")
                    .map(function () {
                        if (!isNaN(parseFloat($(this).val()))) {
                            total_labour_amount += parseFloat($(this).val()) * parseFloat($(this).parent().parent()
                                .find('[name^=labour_quantity]').val());
                        }
                        return parseFloat($(this).val());

                    }).get();
                if (!isNaN(TotalProduct)) {
                    $('[id=total_Product_amount]').html(TotalProduct);
                }
                if (!isNaN(TotalService)) {
                    $('[id=total_Service_amount]').html(TotalService);
                }
                if (!isNaN(total_consumable_amount)) {
                    $('[id=total_consumable_amount]').html('£' + Number(total_consumable_amount).toFixed(2));
                }
                if (!isNaN(total_part_amount)) {
                    $('[id=total_part_amount]').html('£' + Number(total_part_amount).toFixed(2));
                }
                if (!isNaN(total_labour_amount)) {
                    $('[id=total_labour_amount]').html('£' + Number(total_labour_amount).toFixed(2));
                }

                total_grand_amount += TotalService + total_consumable_amount + total_part_amount + total_labour_amount + TotalProduct;
                if (!isNaN(total_grand_amount)) {
                    $('[id=total_grand_amount]').html(total_grand_amount);
                }

                $('[name="brand"]').select2();
                $('[name="model_number"]').select2();
                $('[name="customer_id"]').select2();
                var selectTo = "{{ isset($id) ? $id : null }}";
                if (selectTo == "") {
                    $('.selectToJ1').select2();
                    $('.selectToW1').select2();
                    $('.selectToW2').select2();
                    $('.selectToJ2').select2();
                    $('.selectToWS1').select2();
                    $('.selectToWS2').select2();

                    $('.selectToJ1_es').select2();
                    $('.selectToJ1_es').select2();
                    $('.selectToJ1').select2();
                    $('.selectToW1_es').select2();
                    $('.selectToW2_es').select2();
                    $('.serviceTypeSelect2').select2();

                }

                $(document).on('change', '[name^=customer_id]', function () {
                    var customer_id = $(this).val();
                    $('[name=name]').removeAttr('readonly');
                    $("[name=gst_no]").removeAttr('readonly');
                    $('[name=mobile]').removeAttr('readonly');
                    $("[name=landline]").removeAttr('readonly');
                    $('[name=email]').removeAttr('readonly');
                    $('[name=address]').removeAttr('readonly');
                    $('[name=company_name]').removeAttr('readonly');
                    $('[name=name]').val("");
                    $("[name=gst_no]").val("");
                    $('[name=mobile]').val("");
                    $("[name=landline]").val("");
                    $('[name=email]').val("");
                    $('[name=address]').val("");
                    $('[name=company_name]').val("");
                    $('#registered_vehicleHS').hide();
                    var thisSelf = $(this);
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/getCustomerForWorkshop",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            customer_id: customer_id,
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            var customerDetail = JSON.parse(data);
                            if (customerDetail.length == 0) {
                                $('[name=name]').removeAttr('readonly');
                                $("[name=gst_no]").removeAttr('readonly');
                                $('[name=mobile]').removeAttr('readonly');
                                $("[name=landline]").removeAttr('readonly');
                                $('[name=email]').removeAttr('readonly');
                                $('[name=address]').removeAttr('readonly');
                                $('[name=company_name]').removeAttr('readonly');
                                $('[name=name]').val("");
                                $("[name=gst_no]").val("");
                                $('[name=mobile]').val("");
                                $("[name=landline]").val("");
                                $('[name=email]').val("");
                                $('[name=address]').val("");
                                // $('[name=registered_vehicle]').val('');
                                $('#registered_vehicleHS').hide();
                            } else {
                                $('[name=name]').attr('readonly', 'readonly');
                                $("[name=gst_no]").attr('readonly', 'readonly');
                                $('[name=mobile]').attr('readonly', 'readonly');
                                $("[name=landline]").attr('readonly', 'readonly');
                                $('[name=email]').attr('readonly', 'readonly');
                                $('[name=address]').attr('readonly', 'readonly');
                                $('[name=company_name]').attr('readonly', 'readonly');
                                $('[name=shipping_address_street]').attr('readonly', 'readonly');
                                $('[name=shipping_address_city]').attr('readonly', 'readonly');
                                // $('[name=shipping_address_postcode]').attr('readonly', 'readonly');
                                $('[name=shipping_address_county]').attr('readonly', 'readonly');
                                $('[name=shipping_address_country]').attr('readonly', 'readonly');
                                $('#registered_vehicleHS').show();

                                let customer_address = customerDetail.customer_address;

                                if (!customer_address) {
                                    customer_address = [
                                        customerDetail.shipping_address_street,
                                        customerDetail.shipping_address_city,
                                        customerDetail.shipping_address_postcode,
                                        customerDetail.shipping_address_county,
                                        customerDetail.shipping_address_country
                                    ].filter(Boolean).join(', ');
                                }
                                shipping_address_street = customerDetail.shipping_address_street;
                                shipping_address_city = customerDetail.shipping_address_city;
                                shipping_address_postcode = customerDetail.shipping_address_postcode;
                                shipping_address_county = customerDetail.shipping_address_county;
                                shipping_address_country = customerDetail.shipping_address_country;
                                company_name = customerDetail.company_name;
                                customer_alt_number = customerDetail.customer_alt_number;
                                customer_contact_number = customerDetail.customer_contact_number;
                                customer_email = customerDetail.customer_email;
                                customer_gstin = customerDetail.customer_gstin;
                                customer_name = customerDetail.customer_name;
                                $('[name^=name]').val(customer_name);
                                $('[name^=gst_no]').val(customer_gstin);
                                $('[name^=mobile]').val(customer_contact_number);
                                $('[name^=landline]').val(customer_alt_number);
                                $('[name^=email]').val(customer_email);
                                $('[name^=address]').val(customer_address);
                                $('[name^=company_name]').val(company_name);
                                $('[name=shipping_address_street]').val(shipping_address_street);;
                                $('[name=shipping_address_city]').val(shipping_address_city);;
                                $('[name=shipping_address_postcode]').val(shipping_address_postcode);;
                                $('[name=shipping_address_county]').val(shipping_address_county);;
                                $('[name=shipping_address_country]').val(shipping_address_country);;
                            }
                        }
                    });
                });

                $(document).on("change", '#registered_vehicle', function () {
                    var registered_vehicle = $(this).val();
                    if (!registered_vehicle) {
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/') }}/ajax/GetVehicleDetailFromWorkshop",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            registered_vehicle: registered_vehicle
                        },
                        dataType: 'html',
                        cache: false,
                        success: function (data) {
                            data = JSON.parse(data);
                            $('[name=vehicle_reg_number]').val(data[0].vehicle_reg_number);
                            $('[name=vehicle_reg_number]').attr('readonly', 'readonly');
                            $('[name=vehicle_make]').val(data[0].vehicle_make);
                            $('[name=vehicle_model]').val(data[0].vehicle_model);
                            $('[name=vehicle_year]').val(data[0].vehicle_year);
                            $('[name=vehicle_front_tyre_size]').val(data[0].vehicle_front_tyre_size);
                            $('[name=vehicle_rear_tyre_size]').val(data[0].vehicle_rear_tyre_size);
                            $('[name=vehicle_vin]').val(data[0].vehicle_vin);
                            $('[name=vehicle_first_registered]').val(data[0].vehicle_first_registered);
                            $('[name=vehicle_cc]').val(data[0].vehicle_cc);
                            $('[name=vehicle_engine_number]').val(data[0].vehicle_engine_number);
                            $('[name=vehicle_engine_size]').val(data[0].vehicle_engine_size);
                            $('[name=vehicle_axle]').val(data[0].vehicle_axle);
                            $('[name=vehicle_fuel_type]').val(data[0].vehicle_fuel_type);
                            $('[name=vehicle_mot_expiry_date]').val(data[0].vehicle_mot_expiry_date.split("T")[0]);
                            $('[name=advisor]').val(data[0].advisor);
                            $('[name=notes]').val(data[0].notes);
                        }
                    });

                })
                $('[name=customer_id]').on("change", function () {
                    var customerId = $(this).val();

                    if (customerId) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/') }}/ajax/GetVehicleRegFromWorkshop",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                customer_id: customerId,
                            },
                            dataType: 'html',
                            cache: false,
                            success: function (data) {
                                const vehicleRegNum = JSON.parse(data);

                                $('#registered_vehicle').empty()
                                    .append('<option value="">- Select Vehicle -</option>');
                                vehicleRegNum.forEach(vehicle => {
                                    $('#registered_vehicle').append(`<option value="${vehicle.vehicle_reg_number}">${vehicle.vehicle_reg_number}</option>`);
                                });

                                $('#registered_vehicleHS').show();
                                if (vehicleRegNum.length > 0) {
                                    $('#registered_vehicle').val($('#registered_vehicle option:eq(1)').val()).trigger('change');
                                }
                            }
                        });
                    } else {
                        $('#registered_vehicleHS').hide();
                    }
                });
                var IdForUpdate = "{{ isset($id) ? $id : null }}";
                if (IdForUpdate != "") {
                    $('.estimatedCost').replaceWith("");
                }

                var workshopId = "{{ isset($workshopId) ? $workshopId : null }}";
                if (workshopId != "") {
                    location.href = "{{ url('/') }}/AutoCare/workshop/view/" + workshopId;
                }
            });

        </script>
        <script>
            $(document).ready(function () {
                function resetConsumableForm() {
                    const form = $('#consumableForm');
                    form.find('input[type="text"], input[type="number"], textarea').val('');
                    form.find('select').prop('selectedIndex', 0);
                    form.attr('action', '{{ route("consumables.store") }}');
                    form.find('input[name="_method"]').val('POST');
                    $('#consumableIdInput').val('');
                    $('#consumableFormTitle').text('Add Consumable');
                    $('#currentImagePreview').empty();
                    $('#consumableModalErrorAlert').addClass('d-none');
                    $('#consumableModalSuccessAlert').addClass('d-none');
                    $('#consumableModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();
                }
                function openAddConsumableModal() {
                    resetConsumableForm();
                    $('#addEditConsumableModal').modal('show');
                }
                $('#openAddConsumableFromList').click(function (e) {
                    console.log('test');
                    e.preventDefault();
                    openAddConsumableModal();
                });
                $('#addEditConsumableModal').on('show.bs.modal', function (e) {
                    resetConsumableForm();
                });
                $('#addEditConsumableModal').on('shown.bs.modal', function (e) {
                    $('#modal_consumable_name').focus();
                });
                $('#saveConsumableBtn').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $('#consumableForm');
                    const formData = new FormData(form[0]);
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    if (csrfToken) {
                        formData.append('_token', csrfToken);
                    } else {
                        const tokenFromInput = $('#consumableFormToken').val();
                        if (tokenFromInput) {
                            formData.append('_token', tokenFromInput);
                        } else {
                            console.error("CSRF token not found!");
                            $('#consumableModalErrorAlert').removeClass('d-none');
                            $('#consumableModalErrorList').html('<li>CSRF token is missing. Please refresh the page.</li>');
                            return;
                        }
                    }

                    let isValid = true;
                    let validationErrors = [];

                    const nameValue = $('#modal_consumable_name').val().trim();
                    if (!nameValue) {
                        $('#modal_consumable_name').addClass('is-invalid');
                        $('#modal_consumable_name').siblings('.invalid-feedback').text('Name is required.');
                        validationErrors.push('Name is required.');
                        isValid = false;
                    } else {
                        $('#modal_consumable_name').removeClass('is-invalid');
                        $('#modal_consumable_name').siblings('.invalid-feedback').empty();
                    }

                    const priceValue = parseFloat($('#modal_cost_price').val());
                    if (isNaN(priceValue) || priceValue <= 0) {
                        $('#modal_cost_price').addClass('is-invalid');
                        $('#modal_cost_price').siblings('.invalid-feedback').text('Cost Price is required and must be a number greater than 0.');
                        validationErrors.push('Cost Price is required and must be a number greater than 0.');
                        isValid = false;
                    } else {
                        $('#modal_cost_price').removeClass('is-invalid');
                        $('#modal_cost_price').siblings('.invalid-feedback').empty();
                    }
                    if (!isValid) {
                        $('#consumableModalErrorAlert').removeClass('d-none');
                        $('#consumableModalSuccessAlert').addClass('d-none');
                        $('#consumableModalErrorList').empty();
                        validationErrors.forEach(error => {
                            $('#consumableModalErrorList').append(`<li>${error}</li>`);
                        });
                        return;
                    }
                    $('#consumableModalSuccessAlert').addClass('d-none');
                    $('#consumableModalErrorAlert').addClass('d-none');
                    $('#consumableModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();

                    $.ajax({
                        url: '{{ route("consumables.store") }}',
                        type: form.attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#consumableModalErrorAlert').addClass('d-none');
                            $('#consumableModalErrorList').empty();
                            $('#consumableModalSuccessAlert').removeClass('d-none').find('#consumableModalSuccessMessage').text(response.message || 'Consumable saved successfully!');
                            setTimeout(() => {
                                $('#addEditConsumableModal').modal('hide');
                            }, 1500);

                        },
                        error: function (xhr) {
                            console.error("Error saving consumable:", xhr);
                            $('#consumableModalSuccessAlert').addClass('d-none');
                            $('#consumableModalErrorAlert').removeClass('d-none');
                            let errorHtml = '';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    form.find('.is-invalid').removeClass('is-invalid');
                                    form.find('.invalid-feedback').empty();

                                    for (let field in errors) {
                                        const fieldElement = form.find(`[name="${field}"]`);
                                        if (fieldElement.length) {
                                            fieldElement.addClass('is-invalid');
                                            fieldElement.siblings('.invalid-feedback').text(errors[field][0]);
                                        }
                                        errorHtml += `<li>${errors[field][0]}</li>`;
                                    }
                                } else {
                                    errorHtml = `<li>Validation error occurred, but details are unavailable.</li>`;
                                }
                            } else {
                                errorHtml = `<li>${xhr.responseJSON?.message || 'An error occurred while saving the consumable.'}</li>`;
                            }
                            $('#consumableModalErrorList').html(errorHtml);
                        }
                    });
                });
                $('#addEditConsumableModal').on('hidden.bs.modal', function (e) {
                    if ($('#consumableModal').length) {
                        $('#consumableModal').modal('show');
                        fetchConsumables();
                    } else {
                        console.warn("consumableModal element not found when trying to show it after adding consumable.");
                    }
                });
                //part
                function resetPartForm() {
                    const form = $('#partForm');
                    form.find('input[type="text"], input[type="number"], textarea').val('');
                    form.find('select').prop('selectedIndex', 0);
                    form.attr('action', '{{ route("parts.store") }}');
                    form.find('input[name="_method"]').val('POST');
                    $('#partIdInput').val('');
                    $('#partFormTitle').text('Add part');
                    $('#currentImagePreview').empty();
                    $('#partModalErrorAlert').addClass('d-none');
                    $('#partModalSuccessAlert').addClass('d-none');
                    $('#partModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();
                }
                function openAddPartModal() {
                    resetPartForm();
                    $('#addEditPartModal').modal('show');
                }
                $('#openAddPartFromList').click(function (e) {
                    console.log('test');
                    e.preventDefault();
                    openAddPartModal();
                });
                $('#addEditPartModal').on('show.bs.modal', function (e) {
                    resetPartForm();
                });
                $('#addEditPartModal').on('shown.bs.modal', function (e) {
                    $('#modal_part_name').focus();
                });
                $('#savePartBtn').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $('#partForm');
                    const formData = new FormData(form[0]);
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    if (csrfToken) {
                        formData.append('_token', csrfToken);
                    } else {
                        const tokenFromInput = $('#partFormToken').val();
                        if (tokenFromInput) {
                            formData.append('_token', tokenFromInput);
                        } else {
                            console.error("CSRF token not found!");
                            $('#partModalErrorAlert').removeClass('d-none');
                            $('#partModalErrorList').html('<li>CSRF token is missing. Please refresh the page.</li>');
                            return;
                        }
                    }

                    let isValid = true;
                    let validationErrors = [];

                    const nameValue = $('#modal_part_name').val().trim();
                    if (!nameValue) {
                        $('#modal_part_name').addClass('is-invalid');
                        $('#modal_part_name').siblings('.invalid-feedback').text('Name is required.');
                        validationErrors.push('Name is required.');
                        isValid = false;
                    } else {
                        $('#modal_part_name').removeClass('is-invalid');
                        $('#modal_part_name').siblings('.invalid-feedback').empty();
                    }

                    const qtyValue = $('#modal_part_quantity').val().trim();
                    if (!qtyValue) {
                        $('#modal_part_quantity').addClass('is-invalid');
                        $('#modal_part_quantity').siblings('.invalid-feedback').text('Quantity is required.');
                        validationErrors.push('Quantity is required.');
                        isValid = false;
                    } else {
                        $('#modal_part_quantity').removeClass('is-invalid');
                        $('#modal_part_quantity').siblings('.invalid-feedback').empty();
                    }

                    const priceValue = parseFloat($('#modal_cost_price').val());
                    if (isNaN(priceValue) || priceValue <= 0) {
                        $('#modal_cost_price').addClass('is-invalid');
                        $('#modal_cost_price').siblings('.invalid-feedback').text('Cost Price is required and must be a number greater than 0.');
                        validationErrors.push('Cost Price is required and must be a number greater than 0.');
                        isValid = false;
                    } else {
                        $('#modal_cost_price').removeClass('is-invalid');
                        $('#modal_cost_price').siblings('.invalid-feedback').empty();
                    }
                    if (!isValid) {
                        $('#partModalErrorAlert').removeClass('d-none');
                        $('#partModalSuccessAlert').addClass('d-none');
                        $('#partModalErrorList').empty();
                        validationErrors.forEach(error => {
                            $('#partModalErrorList').append(`<li>${error}</li>`);
                        });
                        return;
                    }
                    $('#partModalSuccessAlert').addClass('d-none');
                    $('#partModalErrorAlert').addClass('d-none');
                    $('#partModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();

                    $.ajax({
                        url: '{{ route("parts.store") }}',
                        type: form.attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#partModalErrorAlert').addClass('d-none');
                            $('#partModalErrorList').empty();
                            $('#partModalSuccessAlert').removeClass('d-none').find('#partModalSuccessMessage').text(response.message || 'part saved successfully!');
                            setTimeout(() => {
                                $('#addEditPartModal').modal('hide');
                            }, 1500);

                        },
                        error: function (xhr) {
                            console.error("Error saving part:", xhr);
                            $('#partModalSuccessAlert').addClass('d-none');
                            $('#partModalErrorAlert').removeClass('d-none');
                            let errorHtml = '';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    form.find('.is-invalid').removeClass('is-invalid');
                                    form.find('.invalid-feedback').empty();

                                    for (let field in errors) {
                                        const fieldElement = form.find(`[name="${field}"]`);
                                        if (fieldElement.length) {
                                            fieldElement.addClass('is-invalid');
                                            fieldElement.siblings('.invalid-feedback').text(errors[field][0]);
                                        }
                                        errorHtml += `<li>${errors[field][0]}</li>`;
                                    }
                                } else {
                                    errorHtml = `<li>Validation error occurred, but details are unavailable.</li>`;
                                }
                            } else {
                                errorHtml = `<li>${xhr.responseJSON?.message || 'An error occurred while saving the part.'}</li>`;
                            }
                            $('#partModalErrorList').html(errorHtml);
                        }
                    });
                });
                $('#addEditPartModal').on('hidden.bs.modal', function (e) {
                    if ($('#partModal').length) {
                        $('#partModal').modal('show');
                        fetchParts();
                    } else {
                        console.warn("partModal element not found when trying to show it after adding part.");
                    }
                });
                //Labour
                function resetLabourForm() {
                    const form = $('#labourForm');
                    form.find('input[type="text"], input[type="number"], textarea').val('');
                    form.find('select').prop('selectedIndex', 0);
                    form.attr('action', '{{ route("labours.store") }}');
                    form.find('input[name="_method"]').val('POST');
                    $('#labourIdInput').val('');
                    $('#labourFormTitle').text('Add Labour');
                    $('#currentLabourImagePreview').empty();
                    $('#labourModalErrorAlert').addClass('d-none');
                    $('#labourModalSuccessAlert').addClass('d-none');
                    $('#labourModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();
                }
                function openAddLabourModal() {
                    resetLabourForm();
                    $('#addEditLabourModal').modal('show');
                }
                $('#openAddLabourFromList').click(function (e) {
                    e.preventDefault();
                    openAddLabourModal();
                });
                $('#addEditLabourModal').on('show.bs.modal', function (e) {
                    resetLabourForm();
                });
                $('#addEditLabourModal').on('shown.bs.modal', function (e) {
                    $('#modal_labour_name').focus();
                });
                $('#saveLabourBtn').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $('#labourForm');
                    const formData = new FormData(form[0]);
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    if (csrfToken) {
                        formData.append('_token', csrfToken);
                    } else {
                        const tokenFromInput = $('#labourFormToken').val();
                        if (tokenFromInput) {
                            formData.append('_token', tokenFromInput);
                        } else {
                            console.error("CSRF token not found for labour form!");
                            $('#labourModalErrorAlert').removeClass('d-none');
                            $('#labourModalErrorList').html('<li>CSRF token is missing. Please refresh the page.</li>');
                            return;
                        }
                    }

                    let isValid = true;
                    let validationErrors = [];

                    // Validate Labour Name
                    const nameValue = $('#modal_labour_name').val().trim();
                    if (!nameValue) {
                        $('#modal_labour_name').addClass('is-invalid');
                        $('#modal_labour_name').siblings('.invalid-feedback').text('Labour Name is required.');
                        validationErrors.push('Labour Name is required.');
                        isValid = false;
                    } else {
                        $('#modal_labour_name').removeClass('is-invalid');
                        $('#modal_labour_name').siblings('.invalid-feedback').empty();
                    }

                    const priceValue = parseFloat($('#modal_labour_cost_price').val());
                    if (isNaN(priceValue) || priceValue <= 0) {
                        $('#modal_labour_cost_price').addClass('is-invalid');
                        $('#modal_labour_cost_price').siblings('.invalid-feedback').text('Labour Cost is required and must be a number greater than 0.');
                        validationErrors.push('Labour Cost is required and must be a number greater than 0.');
                        isValid = false;
                    } else {
                        $('#modal_labour_cost_price').removeClass('is-invalid');
                        $('#modal_labour_cost_price').siblings('.invalid-feedback').empty();
                    }

                    if (!isValid) {
                        $('#labourModalErrorAlert').removeClass('d-none');
                        $('#labourModalSuccessAlert').addClass('d-none');
                        $('#labourModalErrorList').empty();
                        validationErrors.forEach(error => {
                            $('#labourModalErrorList').append(`<li>${error}</li>`);
                        });
                        return;
                    }

                    $('#labourModalSuccessAlert').addClass('d-none');
                    $('#labourModalErrorAlert').addClass('d-none');
                    $('#labourModalErrorList').empty();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();

                    $.ajax({
                        url: '{{ route("labours.store") }}',
                        type: form.attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('#labourModalErrorAlert').addClass('d-none');
                            $('#labourModalErrorList').empty();
                            $('#labourModalSuccessAlert').removeClass('d-none').find('#labourModalSuccessMessage').text(response.message || 'Labour saved successfully!');
                            setTimeout(() => {
                                $('#addEditLabourModal').modal('hide');
                            }, 1500);

                        },
                        error: function (xhr) {
                            console.error("Error saving labour:", xhr);
                            $('#labourModalSuccessAlert').addClass('d-none');
                            $('#labourModalErrorAlert').removeClass('d-none');
                            let errorHtml = '';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    form.find('.is-invalid').removeClass('is-invalid');
                                    form.find('.invalid-feedback').empty();

                                    for (let field in errors) {
                                        const fieldElement = form.find(`[name="${field}"]`);
                                        if (fieldElement.length) {
                                            fieldElement.addClass('is-invalid');
                                            fieldElement.siblings('.invalid-feedback').text(errors[field][0]);
                                        }
                                        errorHtml += `<li>${errors[field][0]}</li>`;
                                    }
                                } else {
                                    errorHtml = `<li>Validation error occurred, but details are unavailable.</li>`;
                                }
                            } else {
                                errorHtml = `<li>${xhr.responseJSON?.message || 'An error occurred while saving the labour.'}</li>`;
                            }
                            $('#labourModalErrorList').html(errorHtml);
                        }
                    });
                });
                $('#addEditLabourModal').on('hidden.bs.modal', function (e) {
                    if ($('#labourModal').length) {
                        $('#labourModal').modal('show');
                        fetchLabours();
                    } else {
                        console.warn("Labour selection modal (e.g., 'labourModal') element not found when trying to show it after adding labour.");
                    }
                });
                //end
                $('#refreshConsumableList').click(function () {
                    fetchConsumables();
                });
                $('#refreshPartList').click(function () {
                    fetchParts();
                });
                $('#refreshLabourList').click(function () {
                    fetchLabours();
                });
                $('#addServiceButton').click(function () {
                    $('#serviceModal').modal('show');
                    fetchServices();
                });
                $('#addConsumableButton').click(function () {
                    $('#consumableModal').modal('show');
                    fetchConsumables();
                });
                $('#addPartButton').click(function () {
                    $('#partModal').modal('show');
                    fetchParts();
                });
                $('#addLabourButton').click(function () {
                    $('#labourModal').modal('show');
                    fetchLabours();
                });
                function fetchServices() {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/') }}/AutoCare/get-services",
                        success: function (response) {
                            serviceData = response.services || [];
                            populateServiceModal(serviceData);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching services:', error);
                        }
                    });
                }
                function fetchConsumables() {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/') }}/AutoCare/get-consumables",
                        success: function (response) {
                            consumableData = response.consumables || [];
                            populateConsumableModal(consumableData);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching consumables:', error);
                        }
                    });
                }
                function fetchParts() {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/') }}/AutoCare/get-parts",
                        success: function (response) {
                            PartData = response.parts || [];
                            populatePartModal(PartData);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching Parts:', error);
                        }
                    });
                }
                function fetchLabours() {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/') }}/AutoCare/get-labours",
                        success: function (response) {
                            labourData = response.labours || [];
                            populateLabourModal(labourData);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching labour:', error);
                        }
                    });
                }
                function populateServiceModal(services) {
                    const serviceList = $('#serviceList');
                    serviceList.empty();

                    // Header row
                    serviceList.append(`
                                <div class="table_box table_header">
                                    <div class="item servicename"><strong>Service Name</strong></div>
                                    <div class="item cost"><strong>Price</strong></div>
                                    <div class="item leadtime"><strong>Lead Time</strong></div>
                                    <div class="item check"><strong>Select</strong></div>
                                </div>
                            `);

                    if (!Array.isArray(services) || services.length === 0) {
                        serviceList.append(`
                                    <div class="table_box">
                                        <div class="item" style="grid-column: 1 / -1;">No services available</div>
                                    </div>
                                `);
                        return;
                    }

                    services.forEach(service => {
                        serviceList.append(`
                                    <label class="table_box">
                                        <div class="item servicename">${service.name}</div>
                                        <div class="item cost">£${parseFloat(service.cost_price).toFixed(2)}</div>
                                        <div class="item leadtime">${service.service_lead_time ?? '-'}</div>
                                        <div class="item check">
                                            <input type="checkbox"
                                                class="service-checkbox"
                                                data-id="${service.service_id}"
                                                data-name="${service.name}"
                                                data-vat="${service.tax_class_id}"
                                                data-price="${service.cost_price}">
                                        </div>
                                    </label>
                                `);
                    });
                }
                function populateConsumableModal(consumables) {
                    const consumableList = $('#consumableList');
                    consumableList.empty();
                    if (!Array.isArray(consumables) || consumables.length === 0) {
                        consumableList.append('<tr><td colspan="3">No consumables available</td></tr>');
                        return;
                    }
                    consumables.forEach(consumable => {
                        consumableList.append(`
                                    <label class="table_box">
                                        <div class="item consumablename">${consumable.consumable_name}</div>
                                        <div class="item consumablecontent">${consumable.content}</div>
                                        <div class="item cost">£${parseFloat(consumable.cost_price).toFixed(2)}</div>
                                        <div class="item check"><input type="checkbox" class="consumable-checkbox" // Renamed class
                                            data-id="${consumable.consumable_id}" // Assuming data structure has consumable_id
                                            data-name="${consumable.consumable_name}"
                                             data-content="${consumable.content}"
                                            data-vat="${consumable.tax_class_id}"
                                            data-price="${consumable.cost_price}">
                                            </div>
                                    </label>
                                `);
                    });
                }
                function populatePartModal(parts) {
                    const partList = $('#partList');
                    partList.empty();
                    if (!Array.isArray(parts) || parts.length === 0) {
                        partList.append('<tr><td colspan="3">No parts available</td></tr>');
                        return;
                    }
                    parts.forEach(part => {
                        partList.append(`
                                    <label class="table_box">
                                        <div class="item partname">${part.part_name}</div>
                                        <div class="item partcontent">${part.content}</div>
                                        <div class="item cost">£${parseFloat(part.cost_price).toFixed(2)}</div>
                                        <div class="item check"><input type="checkbox" class="part-checkbox" // Renamed class
                                            data-id="${part.part_id}" // Assuming data structure has part_id
                                            data-name="${part.part_name}"
                                             data-content="${part.content}"
                                            data-vat="${part.tax_class_id}"
                                            data-price="${part.cost_price}"></div>
                                    </label>
                                `);
                    });
                }
                function populateLabourModal(labourItems) {
                    const labourList = $('#labourList');
                    labourList.empty();
                    if (!Array.isArray(labourItems) || labourItems.length === 0) {
                        labourList.append('<tr><td colspan="3">No labour items available</td></tr>');
                        return;
                    }
                    labourItems.forEach(labour => {
                        labourList.append(`
                                    <label class="table_box">
                                        <div class="item partname">${labour.labour_name}</div>
                                        <div class="item partcontent">${labour.content}</div> 
                                        <div class="item cost">£${parseFloat(labour.cost_price).toFixed(2)}</div>
                                        <div class="item check"><input type="checkbox" class="labour-checkbox"
                                            data-id="${labour.labour_id}"
                                            data-name="${labour.labour_name}"
                                             data-content="${labour.content}"
                                            data-vat="${labour.tax_class_id}"
                                            data-price="${labour.cost_price}"></div>
                                    </label>
                                `);
                    });
                }
                $('#addSelectedConsumables').click(function () {
                    let selectedConsumables = [];
                    $('.consumable-checkbox:checked').each(function () {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let content = $(this).data('content');
                        let price = parseFloat($(this).data('price')) || 0;
                        let vatClass = $(this).data('vat');
                        let vatRate = (vatClass === 9) ? 0.2 : 0.0;
                        let vatAmount = price * vatRate;
                        let total = price + vatAmount;
                        selectedConsumables.push(`
                                    <tr>
                                        <input type="hidden" name="consumable_id[]" value="${id}">
                                        <td><input type="hidden" name="consumable_name[]" value="${name}">${name}</td>
                                        <td><textarea class="form-control" name="consumable_content[]" value="${content}">${content}</textarea></td>
                                        <td><input type="number" name="consumable_quantity[]" class="form-control quantity" value="1" min="1" data-price="${price}"></td>
                                        <td><input type="number" step="0.01" name="consumable_price[]" class="form-control price" value="${price.toFixed(2)}"></td>
                                        <td>
                                            <input type="hidden" name="consumable_vat[]" value="${vatClass}">
                                            <select name="consumable_vat_rate[]" class="form-control vat-rate">
                                                <option value="0" ${vatClass === 0 ? 'selected' : ''}>0% VAT</option>
                                                <option value="0.2" ${vatClass === 9 ? 'selected' : ''}>20% VAT</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="consumable_total[]" class="form-control consumable-total" value="${total.toFixed(2)}"></td> <!-- Renamed name and class -->
                                        <td><button type="button" class="btn btn-danger remove-consumable">Remove</button></td> <!-- Renamed class -->
                                    </tr>
                                `);
                    });
                    $('#selectedConsumablesTable tbody').append(selectedConsumables);
                    $('#consumableModal').modal('hide');
                    updateTotals();
                });
                $('#addSelectedParts').click(function () {
                    let selectedParts = [];
                    $('.part-checkbox:checked').each(function () {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let content = $(this).data('content');
                        let price = parseFloat($(this).data('price')) || 0;
                        let vatClass = $(this).data('vat');
                        let vatRate = (vatClass === 9) ? 0.2 : 0.0;
                        let vatAmount = price * vatRate;
                        let total = price + vatAmount;
                        selectedParts.push(`
                                    <tr>
                                        <input type="hidden" name="part_id[]" value="${id}">
                                        <td><input type="hidden" name="part_name[]" value="${name}">${name}</td>
                                        <td><textarea class="form-control" name="part_content[]" value="${content}">${content}</textarea></td>
                                        <td><input type="number" name="part_quantity[]" class="form-control quantity" value="1" min="1" data-price="${price}"></td>
                                        <td><input type="number" step="0.01" name="part_price[]" class="form-control price" value="${price.toFixed(2)}"></td>
                                        <td>
                                            <input type="hidden" name="part_vat[]" value="${vatClass}">
                                            <select name="part_vat_rate[]" class="form-control vat-rate">
                                                <option value="0" ${vatClass === 0 ? 'selected' : ''}>0% VAT</option>
                                                <option value="0.2" ${vatClass === 9 ? 'selected' : ''}>20% VAT</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="part_total[]" class="form-control part-total" value="${total.toFixed(2)}"></td> <!-- Renamed name and class -->
                                        <td><button type="button" class="btn btn-danger remove-part">Remove</button></td> <!-- Renamed class -->
                                    </tr>
                                `);
                    });
                    $('#selectedPartsTable tbody').append(selectedParts);
                    $('#partModal').modal('hide');
                    updateTotals();
                });
                $('#addSelectedLabours').click(function () {
                    let selectedLabour = [];
                    $('.labour-checkbox:checked').each(function () {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let content = $(this).data('content');
                        let price = parseFloat($(this).data('price')) || 0;
                        let vatClass = $(this).data('vat');
                        let vatRate = (vatClass === 9) ? 0.2 : 0.0;
                        let vatAmount = price * vatRate;
                        let total = price + vatAmount;

                        selectedLabour.push(`
                                    <tr>
                                        <input type="hidden" name="labour_id[]" value="${id}"> <!-- New name -->
                                        <td><input type="hidden" name="labour_name[]" value="${name}">${name}</td> <!-- New name -->
                                        <td><textarea class="form-control" name="labour_content[]" value="${content}">${content}</textarea></td>
                                        <td><input type="number" name="labour_quantity[]" class="form-control quantity" value="1" min="1" data-price="${price}"></td> <!-- New name -->
                                        <td><input type="number" step="0.01" name="labour_price[]" class="form-control price" value="${price.toFixed(2)}"></td> <!-- New name -->
                                        <td>
                                            <input type="hidden" name="labour_vat[]" value="${vatClass}"> <!-- New name -->
                                            <select name="labour_vat_rate[]" class="form-control vat-rate"> <!-- New name -->
                                                <option value="0" ${vatClass === 0 ? 'selected' : ''}>0% VAT</option>
                                                <option value="0.2" ${vatClass === 9 ? 'selected' : ''}>20% VAT</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="labour_total[]" class="form-control labour-total" value="${total.toFixed(2)}"></td> <!-- New name and class -->
                                        <td><button type="button" class="btn btn-danger remove-labour">Remove</button></td> <!-- New class -->
                                    </tr>
                                `);
                    });
                    $('#selectedLaboursTable tbody').append(selectedLabour);
                    $('#labourModal').modal('hide');
                    updateTotals();
                });
                $('#addSelectedServices').click(function () {
                    let selectedServices = [];
                    $('.service-checkbox:checked').each(function () {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let price = parseFloat($(this).data('price')) || 0;
                        let vatClass = $(this).data('vat');
                        let vatRate = (vatClass === 9) ? 0.2 : 0.0;
                        let vatAmount = price * vatRate;
                        let total = price + vatAmount;

                        selectedServices.push(`
                                    <tr>
                                        <input type="hidden" name="service_id[]" value="${id}">
                                        <td><input type="hidden" name="service_name[]" value="${name}">${name}</td>
                                        <td><input type="number" name="service_quantity[]" class="form-control quantity" value="1" min="1" data-price="${price}"></td>
                                        <td><input type="number" step="0.01" name="service_price[]" class="form-control price" value="${price.toFixed(2)}"></td>
                                        <td>
                                            <input type="hidden" name="service_vat[]" value="${vatClass}">
                                            <select name="service_vat_rate[]" class="form-control vat-rate">
                                                <option value="0" ${vatClass === 0 ? 'selected' : ''}>0% VAT</option>
                                                <option value="0.2" ${vatClass === 9 ? 'selected' : ''}>20% VAT</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="service_total[]" class="form-control service-total" value="${total.toFixed(2)}"></td>
                                        <td><button type="button" class="btn btn-danger remove-service">Remove</button></td>
                                    </tr>
                                `);
                    });
                    $('#selectedServicesTable tbody').append(selectedServices);
                    $('#serviceModal').modal('hide');
                    updateTotals();
                });

                $(document).on('change', '.vat-rate, .quantity, .price', function () {
                    let row = $(this).closest('tr');
                    let quantity = parseFloat(row.find('.quantity').val()) || 1;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let vatRate = parseFloat(row.find('.vat-rate').val()) || 0;

                    let subtotal = price * quantity;
                    let vatAmount = subtotal * vatRate;
                    let total = subtotal + vatAmount;

                    if (row.closest('#selectedServicesTable').length) {
                        row.find('.service-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedConsumablesTable').length) {
                        row.find('.consumable-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedPartsTable').length) {
                        row.find('.part-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedLaboursTable').length) {
                        row.find('.labour-total').val(total.toFixed(2));
                        updateTotals();
                    }
                    updateTotals();
                });
                $(document).on('input', '.quantity, .price', function () {
                    let row = $(this).closest('tr');
                    let quantity = parseFloat(row.find('.quantity').val()) || 1;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let vatRate = parseFloat(row.find('.vat-rate').val()) || 0;

                    let subtotal = price * quantity;
                    let vatAmount = subtotal * vatRate;
                    let total = subtotal + vatAmount;

                    if (row.closest('#selectedServicesTable').length) {
                        row.find('.service-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedConsumablesTable').length) {
                        row.find('.consumable-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedPartsTable').length) {
                        row.find('.part-total').val(total.toFixed(2));
                        updateTotals();
                    } else if (row.closest('#selectedLaboursTable').length) {
                        row.find('.labour-total').val(total.toFixed(2));
                        updateTotals();
                    }
                    updateTotals();
                });
                $(document).on('click', '.remove-service', function () {
                    $(this).closest('tr').remove();
                    updateTotals();
                });
                $(document).on('click', '.remove-consumable', function () {
                    $(this).closest('tr').remove();
                    updateTotals();
                });
                $(document).on('click', '.remove-part', function () {
                    $(this).closest('tr').remove();
                    updateTotals();
                });
                $(document).on('click', '.remove-labour', function () {
                    $(this).closest('tr').remove();
                    updateTotals();
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                $('#customer_id').select2({
                    placeholder: 'Search by Name or Company',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '/customers/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.text,
                                    };
                                }),
                            };
                        },
                        cache: true,
                    },
                    minimumInputLength: 2,
                });
            });
        </script>

        <script>
            $(document).ready(function () {
                let tyreProducts = [];
                let currentFilters = {};
                let activeProductType = 'tyre';
                $('#addTyreButton').click(function () {
                    activeProductType = 'tyre';
                    const selectedFittingType = $('#fitting_type').val();
                    $('#tyreModal').modal('show');
                    if (selectedFittingType) {
                        $('#fittingtype').val(selectedFittingType).trigger('change');
                        applyFilters();
                    }
                });


                function populateSupplierFilter() {
                    const supplierDropdown = $('#supplier');
                    if (supplierDropdown.children().length > 1) return;

                    $.ajax({
                        type: "GET",
                        url: "{{ route('AutoCare.workshop.getSuppliers') }}",
                        success: function (response) {
                            const suppliers = response.suppliers;
                            if (Array.isArray(suppliers)) {
                                supplierDropdown.empty();
                                supplierDropdown.append('<option value="">-- Select Supplier --</option>');

                                suppliers.forEach((supplier) => {
                                    const option = `<option value="${supplier.supplier_name}" ${supplier.supplier_name === 'ownstock' ? 'selected' : ''
                                        }>${supplier.supplier_name}</option>`;
                                    supplierDropdown.append(option);
                                });
                            } else {
                                console.error('Invalid supplier data format:', response);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching suppliers:', error);
                        }
                    });
                }

                function populateOrdertypeFilter(selectedValue = '19') {
                    const orderTypeDropdown = $('#fittingtype');
                    if (orderTypeDropdown.find('option').length > 0) return;

                    $.ajax({
                        type: "GET",
                        url: "{{ route('AutoCare.workshop.getOrderType') }}",
                        success: function (response) {
                            const orderTypes = response.ordertype_name;
                            if (Array.isArray(orderTypes)) {
                                orderTypeDropdown.empty();
                                let hasSelected = false;
                                orderTypes.forEach((orderType) => {
                                    const isSelected = orderType.ordertype_name === selectedValue ? 'selected' : '';
                                    if (isSelected) hasSelected = true;
                                    orderTypeDropdown.append(`<option value="${orderType.ordertype_name}" ${isSelected}>${orderType.ordertype_name.replace(/_/g, ' ').toUpperCase()}</option> `);
                                });
                                if (!hasSelected && orderTypes.length > 0) {
                                    orderTypeDropdown.val(orderTypes[0].ordertype_name);
                                }
                            } else {
                                console.error('Invalid order type data format:', response);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching order types:', error);
                        }
                    });
                }

                let currentPage = 1;
                let perPage = 10;

                function fetchTyreProducts(page = 1) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('getTyreProducts') }}",
                        data: {
                            page,
                            ...currentFilters // Use stored filters
                        },
                        success: function (response) {
                            const products = response.tyre_products?.data || [];
                            const pagination = response.tyre_products || {};
                            populateTable(products);
                            setupPagination(pagination);
                            populateSupplierFilter();
                            populateOrdertypeFilter(currentFilters.fittingtype);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching tyre products:', error);
                        }
                    });
                }

                function setupPagination(paginationData) {
                    const { current_page, last_page } = paginationData;
                    const paginationControls = $('#paginationControls');
                    paginationControls.empty();

                    if (!last_page || last_page <= 1) return;

                    function goToPage(page) {
                        if (activeProductType === 'tyre') {
                            fetchTyreProducts(page);
                        }
                    }

                    const prevButton = $(`<button type="button" class="btn btn-sm mx-1 ${current_page === 1 ? 'btn-secondary' : 'btn-primary'}" ${current_page === 1 ? 'disabled' : ''}>Previous</button>`);
                    prevButton.click((e) => {
                        e.preventDefault();
                        goToPage(current_page - 1);
                    });
                    paginationControls.append(prevButton);

                    const maxVisiblePages = 5;
                    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
                    let endPage = Math.min(last_page, startPage + maxVisiblePages - 1);

                    if (endPage - startPage < maxVisiblePages - 1) {
                        startPage = Math.max(1, endPage - maxVisiblePages + 1);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const pageButton = $(`<button type="button" class="btn btn-sm mx-1 ${i === current_page ? 'btn-primary' : 'btn-light'}">${i}</button>`);
                        pageButton.click((e) => {
                            e.preventDefault();
                            goToPage(i);
                        });
                        paginationControls.append(pageButton);
                    }

                    const nextButton = $(`<button type="button" class="btn btn-sm mx-1 ${current_page === last_page ? 'btn-secondary' : 'btn-primary'}" ${current_page === last_page ? 'disabled' : ''}>Next</button>`);
                    nextButton.click((e) => {
                        e.preventDefault();
                        goToPage(current_page + 1);
                    });
                    paginationControls.append(nextButton);
                }

                function populateTable(products) {
                    const tyreList = $('#tyreList');
                    tyreList.empty();

                    if (!Array.isArray(products) || products.length === 0) {
                        tyreList.append('<tr><td colspan="14">No products found</td></tr>');
                        return;
                    }

                    products.forEach((product) => {
                        tyreList.append(`
                                                <tr>
                                                <td>${product.tyre_brand_name || ''}</td>
                                                <td>${product.tyre_ean} ${product.tyre_description || ''} ${product.tyre_width}/${product.tyre_profile}R${product.tyre_diameter}</td>
                                                <td>${product.tyre_fuel || ''}</td>
                                                <td>${product.tyre_wetgrip || ''}</td>
                                                <td>${product.tyre_noisedb || ''}</td>
                                                <td>${product.vehicle_type || ''}</td>
                                                <td>${product.tyre_runflat || ''}</td>
                                                <td>${product.tyre_season || ''}</td>
                                                <td>${product.tyre_quantity || ''}</td>
                                                <td>${!isNaN(parseFloat(product.tyre_price)) ? parseFloat(product.tyre_price).toFixed(2) : ''}</td>
                                                <td>${product.tax_class_id === 9
                                ? (parseFloat(product.selected_price || 0) * 1.2).toFixed(2)
                                : parseFloat(product.selected_price || 0).toFixed(2)
                            }</td>
                                                <td>${product.trade_costprice || ''}</td>
                                                <td>${product.tyre_supplier_name || ''}</td>
                                                <td><button class="btn btn-primary selectTyre" data-product-id="${product.product_id || ''}">+Add</button></td>
                                                </tr>
                                                `);
                    });
                    attachSelectEvent(products);
                }

                function applyFilters() {
                    let rftFilterValue = document.getElementById('rftFilter').checked ? 1 : 0;

                    currentFilters = {
                        tyre_ean: $('#eanFilter').val(),
                        tyre_sku: $('#skuFilter').val(),
                        tyre_width: $('#widthFilter').val(),
                        tyre_profile: $('#profileFilter').val(),
                        tyre_diameter: $('#diameterFilter').val(),
                        tyre_brand_name: $('#brandFilter').val(),
                        tyre_supplier_name: $('#supplier').val(),
                        tyre_runflat: rftFilterValue,
                        fittingtype: $('#fittingtype').val(),
                        tyre_price: $('#priceFilter').val(),
                    };

                    fetchTyreProducts(1);
                }

                $(document).ready(function () {
                    fetchTyreProducts();
                    $('#searchButton').click(function (event) {
                        event.preventDefault();
                        applyFilters();
                    });
                });


                function attachSelectEvent(products) {
                    $(document).off('click', '.selectTyre').on('click', '.selectTyre', function (event) {
                        event.preventDefault();
                        const productId = $(this).data('product-id');
                        const selectedProduct = products.find((product) => product.product_id === productId);

                        if (selectedProduct) {
                            const selectedPrice = parseFloat(selectedProduct.selected_price) || 0;
                            const tyre_price = parseFloat(selectedProduct.tyre_price) || 0;
                            const rate = (selectedPrice - tyre_price) + tyre_price;
                            const vatType = selectedProduct.tax_class_id === 9 ? 9 : 0;
                            const amount = rate * (1 + vatType / 100);
                            const itemValue = ` ${selectedProduct.tyre_description}`;
                            const descriptionValue = `<strong>${selectedProduct.tyre_ean}</strong> ${itemValue}`;

                            const newRow = `
                                                    <tr>
                                                    <input type="hidden" name="product_id[]" value="${selectedProduct.product_id}" class="form-control" required>
                                                    <input type="hidden" name="tyre_ean[]" value="${selectedProduct.tyre_ean}">
                                                    <input type="hidden" name="tyre_sku[]" value="${selectedProduct.tyre_sku}">
                                                    <input type="hidden" name="tyre_supplier_name[]" value="${selectedProduct.tyre_supplier_name}">
                                                    <input type="hidden" name="product_type" value="tyre">
                                                    <input type="hidden" name="item[]" value="${itemValue}" class="form-control" required>
                                                    <input type="hidden" name="tyre_description[]" value="${selectedProduct.tyre_description}" class="form-control" required>
                                                    <td> ${itemValue}</td>
                                                    <td> ${descriptionValue}</td>
                                                    <td>
                                                    <input type="number" name="tyre_quantity[]" value="1" min="1" class="form-control" required>
                                                    <small class="stock-error text-danger"></small>
                                                    </td>
                                                    <td>
                                                    <input type="number" step="0.01" name="tyre_cost_price[]"  value="${tyre_price.toFixed(2)}" class="form-control" required>
                                                    </td>
                                                    <td>
                                                    <input type="number" step="0.01" name="tyre_margin_rate[]"  value="${rate.toFixed(2)}" class="form-control">
                                                    </td>
                                                    <td>
                                                    <select name="tyre_vat[]" class="form-control vat-type">
                                                    <option value="9" ${vatType === 9 ? "selected" : ""}>20% VAT</option>
                                                    <option value="0" ${vatType === 0 ? "selected" : ""}>No VAT</option>
                                                    </select>
                                                    </td>
                                                    <td>
                                                    <input type="number" step="0.01" name="tyre_amount[]" value="${amount.toFixed(2)}" step="0.01" class="form-control">
                                                    </td>
                                                    <td>
                                                    <button type="button" class="btn btn-danger removeRow">Delete</button>
                                                    </td>
                                                    </tr>
                                                    `;
                            const newRowEl = $(newRow);
                            $('#tBodyForProductTable').append(newRowEl);
                            validateStock(newRowEl);
                        }

                        $('#tyreModal').modal('hide');
                        updateTotals();
                    });
                }

                $(document).on('input', 'input[name="tyre_quantity[]"],input[name="tyre_cost_price[]"], input[name="tyre_margin_rate[]"], input[name="tyre_vat[]"]', function () {
                    updateTotals();
                });

                $(document).on('change input', 'input[name="tyre_quantity[]"]', function () {
                    const row = $(this).closest('tr');
                    const productId = row.find('input[name="product_id[]"]').val();
                    if (productId) {
                        validateStock(row);
                    }
                });


                $(document).on('click', '.removeRow', function () {
                    $(this).closest('tr').remove();
                    updateTotals();
                });

                $(document).on('change', 'select[name="tyre_vat[]"]', function () {
                    updateTotals();
                });

                updateTotals();
            });

function updateTotals() {
    let totalProductPrice = 0;
    let totalServicePrice = 0;
    let totalPartPrice = 0;
    let totalConsumablePrice = 0;
    let totalLabourPrice = 0;
    let shippingPrice = 0;
    let shippingVat = 0;
    let garageFittingPrice = 0;
    let garageFittingVat = 0;
    let totalTyreQuantity = 0;

    // 1. Calculate Tyre Totals & Quantity
    $('#tBodyForProductTable tr').each(function () {
        const quantity = parseFloat($(this).find('input[name="tyre_quantity[]"]').val()) || 0;
        totalTyreQuantity += quantity;
        
        const rate = parseFloat($(this).find('input[name="tyre_margin_rate[]"]').val()) || 0;
        const vatType = parseFloat($(this).find('select[name="tyre_vat[]"]').val()) || 0;
        const amount = (rate * quantity) * (1 + (vatType === 9 ? 0.2 : 0));
        $(this).find('input[name="tyre_amount[]"]').val(amount.toFixed(2));
        totalProductPrice += amount;
    });

    // 2. Calculate Other Totals
    $('#selectedServicesTable tbody tr').each(function () {
        totalServicePrice += parseFloat($(this).find('.service-total').val()) || 0;
    });
    $('#selectedConsumablesTable tbody tr').each(function () {
        totalConsumablePrice += parseFloat($(this).find('.consumable-total').val()) || 0;
    });
    $('#selectedPartsTable tbody tr').each(function () {
        totalPartPrice += parseFloat($(this).find('.part-total').val()) || 0;
    });
    $('#selectedLaboursTable tbody tr').each(function () {
        totalLabourPrice += parseFloat($(this).find('.labour-total').val()) || 0;
    });

    // 3. Calculate Shipping (Mobile/Mailorder)
    if (['mobile_fitted', 'mailorder'].includes($('#fitting_type').val()) && window.postcodeResponseData){
        shippingPrice = parseFloat(window.postcodeResponseData.ship_price) || 0;
        shippingVat = shippingPrice * 0.2;
    }

    // 4. Calculate Garage Fitting - ONLY WHEN FULLY_FITTED
    if ($('#fitting_type').val() === 'fully_fitted' && window.garageFittingResponseData) {
        const perTyrePrice = window.garageFittingResponseData.fitting_price || 0;
        const vatClass = window.garageFittingResponseData.includes_vat || 0;
        const vatRate = (vatClass == 9) ? 0.2 : 0;
        
        const baseGarageTotal = perTyrePrice * totalTyreQuantity;
        const garageVatAmount = baseGarageTotal * vatRate;
        
        garageFittingPrice = baseGarageTotal;
        garageFittingVat = garageVatAmount;

        const grandGarageTotal = garageFittingPrice + garageFittingVat;
        $('#garageFittingCharges').text('£' + grandGarageTotal.toFixed(2));
        $('#garage_fitting_charges_input').val(garageFittingPrice.toFixed(2));
        $('#garage_fitting_vat_input').val(vatClass);
        $('#total_garage_fitting_input').val(grandGarageTotal.toFixed(2));
    } else {
        // Clear garage charges for non-fully_fitted types
        $('#garageFittingCharges').text('£0.00');
        $('#garage_fitting_charges_input').val(0);
        $('#garage_fitting_vat_input').val(0);
        $('#total_garage_fitting_input').val(0);
    }

    // 5. Calculate Grand Total
    const grandTotal = totalProductPrice + totalServicePrice + totalConsumablePrice + 
                      totalPartPrice + totalLabourPrice + shippingPrice + shippingVat + 
                      garageFittingPrice + garageFittingVat;

    // 6. Update UI
    $('#total_Product_amount').text(totalProductPrice.toFixed(2));
    $('#total_Service_amount').text(totalServicePrice.toFixed(2));
    $('#total_consumable_amount').text(totalConsumablePrice.toFixed(2));
    $('#total_part_amount').text(totalPartPrice.toFixed(2));
    $('#total_labour_amount').text(totalLabourPrice.toFixed(2));
    $('#total_grand_amount').text(grandTotal.toFixed(2));
    
    // Update Hidden Inputs
    $('#total_tyre_price_input').val(totalProductPrice.toFixed(2));
    $('#total_service_price_input').val(totalServicePrice.toFixed(2));
    $('#total_consumable_price_input').val(totalConsumablePrice.toFixed(2));
    $('#total_part_price_input').val(totalPartPrice.toFixed(2));
    $('#total_labour_price_input').val(totalLabourPrice.toFixed(2));
    $('#grand_total_input').val(grandTotal.toFixed(2));
}


            $('form').on('submit', function (e) {
                if (!allRowsValid) {
                    e.preventDefault();
                    alert('One or more tyres exceed available stock. Please adjust quantities.');
                    return false;
                }
            });
        </script>

        <script>
            document.getElementById('lookupButton').addEventListener('click', async function () {
                const lookupButton = document.getElementById('lookupButton');
                const vrm = document.getElementById('vehicle_reg_number').value.trim();

                lookupButton.disabled = true;
                lookupButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Fetching...';

                if (!vrm) {
                    alert('Please enter a valid VRM.');
                    lookupButton.disabled = false;
                    lookupButton.innerHTML = 'Lookup';
                    return;
                }

                try {
                    const response = await fetch(`${window.location.origin}/vehicle-mot-data?vrm=${vrm}`, {
                        headers: {
                            'X-Request-Token': '{{ env("API_REQUEST_TOKEN") }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.status === 429) {
                        alert('Too many requests! Please wait a moment before trying again.');
                        return;
                    }

                    const result = await response.json();
                    lookupButton.innerHTML = 'Lookup';

                    if (response.ok && result.success && result.data) {
                        const vehicleDetails = result.data.VehicleDetails?.VehicleIdentification || {};
                        const SmmtDetails = result.data.SmmtDetails?.TechnicalDetails || {};
                        const tyreDetails = result.data.TyreDetails?.TyreDetailsList?.[0] || {};
                        const motHistory = result.data.MotHistoryDetails?.MotDueDate || {};

                        document.getElementById('vehicle_make').value = vehicleDetails.DvlaMake || '';
                        document.getElementById('vehicle_model').value = vehicleDetails.DvlaModel || '';
                        document.getElementById('vehicle_vin').value = vehicleDetails.Vin || '';
                        document.getElementById('vehicle_first_registered').value = vehicleDetails.YearOfManufacture || '';

                        document.getElementById('vehicle_engine_size').value = SmmtDetails.EngineCapacityCc || '';
                        document.getElementById('vehicle_axle').value = SmmtDetails.DriveType || '';
                        document.getElementById('vehicle_cc').value = SmmtDetails.EngineCapacityCc || '';
                        document.getElementById('vehicle_fuel_type').value = SmmtDetails.FuelType || '';
                        document.getElementById('vehicle_engine_number').value = vehicleDetails.EngineNumber || '';
                        document.getElementById('vehicle_front_tyre_size').value = tyreDetails.Front?.Tyre?.SizeDescription || '';
                        document.getElementById('vehicle_rear_tyre_size').value = tyreDetails.Rear?.Tyre?.SizeDescription || '';
                        document.getElementById('vehicle_mot_expiry_date').value = motHistory.split("T")[0] || '';
                    } else {
                        alert(result.error || 'Unable to fetch VRM details.');
                    }
                } catch (error) {
                    console.error('Fetch Error:', error.message);
                    alert('An error occurred while fetching VRM details.');
                    lookupButton.innerHTML = 'Lookup';
                } finally {
                    lookupButton.disabled = false;
                }
            });

        </script>
        <script>
            $(document).ready(function () {
                const fittingTypeDropdown = $('#fitting_type');

                // Function to populate the fitting type dropdown
                function populateFittingType(selectedValue = 'fully_fitted') {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('AutoCare.workshop.getOrderType') }}",
                        success: function (response) {
                            const orderTypes = response.ordertype_name;

                            if (Array.isArray(orderTypes)) {
                                // Clear existing options
                                fittingTypeDropdown.empty();
                                orderTypes.forEach((orderType) => {
                                    const isSelected = orderType.ordertype_name === selectedValue ? 'selected' : '';
                                    fittingTypeDropdown.append(`
                                            <option value="${orderType.ordertype_name}" ${isSelected}>
                                                ${orderType.ordertype_name.replace(/_/g, ' ').toUpperCase()}
                                            </option>
                                        `);
                                });

                                if (!fittingTypeDropdown.find('option:selected').length) {
                                    fittingTypeDropdown.val('fully_fitted');
                                }
                            } else {
                                console.error('Invalid order type data format:', response);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching order types:', error);
                        }
                    });
                }
                const fittingTypeFromBackend = '{{ isset($fitting_type) ? $fitting_type : null }}';
                populateFittingType(fittingTypeFromBackend || 'fully_fitted');
            });
        </script>
@endsection