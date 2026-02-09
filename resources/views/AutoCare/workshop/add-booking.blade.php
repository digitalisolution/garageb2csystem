        {{ Form::open([
        'url' => 'AutoCare/workshop/add',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
        'autocomplete' => 'OFF'
    ]) }}
            {{ csrf_field() }}
            {{ Form::hidden('id', isset($id) ? $id : '', []) }}
            <div class="">
                <h5>Please Fill Up Workshop Details</h5>


                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12 col-sm-12">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <!-- /.box-header -->
                            <!-- form start -->
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
                                        <h6><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h6>
                                        {!! session('message.content') !!}

                                        @if (isset($id))
                                            <div class="text-center text-primary"><a id="openWorkshopDetail"
                                                    href="{{ url('/') }}/AutoCare/workshop/view/{{ isset($id) ? $id : '' }}">Show
                                                    Detail</a></div>
                                            <script type="text/javascript">
                                                $(document).ready(function () {
                                                    setTimeout(function () {
                                                        $('#openWorkshopDetail').trigger('click');
                                                        var newTab = window.open(
                                                            "{{ url('/') }}/AutoCare/workshop/search");
                                                        // console.log("Worshop Detail Opened In New Tab");
                                                    }, 1000)
                                                })
                                            </script>
                                        @else
                                            <div class="text-center text-primary"><a id="openWorkshopDetail"
                                                    href="{{ url('/') }}/AutoCare/workshop/view/{{ isset($workshopId) ? $workshopId : '' }}">Show
                                                    Detail</a></div>
                                            <script type="text/javascript">
                                                $(document).ready(function () {
                                                    setTimeout(function () {
                                                        $('#openWorkshopDetail').trigger('click');
                                                        var newTab = window.open(
                                                            "{{ url('/') }}/AutoCare/workshop/search");
                                                        // console.log("Worshop Detail Opened In New Tab");
                                                    }, 1000)
                                                })
                                            </script>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-header with-border">
                    <h6>New Job Card</h6>
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
                                            {{ Form::select(
            'customer_id',
            $customerNameSelect,
            isset($customer_id) ? $customer_id : '',
            ['class' => 'form-control', 'id' => 'customer_id', 'placeholder' => 'Select Customer']
        ) }}
        <div class="input-group-append">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
        <i class="fa fa-plus"></i>
    </button></div>
</div>


                                            <div class="invalid-feedback">
                                                {{ $errors->has('customer_id') ? $errors->first('customer_id', ':message') : '' }}
                                            </div>
                                        </div>
                        @endif

                        <div class="col-md-3">

                            <label class="control-label" for="name"> Name:&emsp;</label>
                            {{ Form::text('name', isset($name) ? $name : '', [
        'class' => 'form-control',
        'id' => 'name',
        'placeholder' => ' Name'
    ]) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('name') ? $errors->first('name', ':message') : '' }}
                            </div>

                        </div>
                        <div class="col-md-3">

                            <label class="control-label" for="mobile">Contact Number:&emsp;</label>
                            {{ Form::number('mobile', isset($mobile) ? $mobile : '', [
        'class' => 'form-control ',
        'id' =>
            'mobile',
        'placeholder' => ' mobile'
    ]) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('mobile') ? $errors->first('mobile', ':message') : '' }}

                            </div>
                        </div>
                        <div class="col-md-3">

                            <label class="control-label" for="email">Email:&emsp;</label>
                            {{ Form::email('email', isset($email) ? $email : '', [
        'class' => 'form-control ',
        'placeholder' =>
            'Email'
    ]) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('email') ? $errors->first('email', ':message') : '' }}
                            </div>

                        </div>

                        @if (!isset($id))
                                        <div class="col-md-3" id="registered_vehicleHS" style="display:none">
                                            <label class="control-label" for="registered_vehicle">Get Vehicle By Reg Number
                                                <span class="text-red">*</span>
                                            </label>
                                            {{ Form::select('registered_vehicle', $registered_vehicle_select, isset($registered_vehicle) ?
            $registered_vehicle : '', [
            'class' => 'form-control',
            'id' => 'registered_vehicle',
            'placeholder' =>
                'Select Vehicle Number'
        ]) }}
                                            <div class="invalid-feedback">
                                                {{ $errors->has('registered_vehicle') ? $errors->first('registered_vehicle', ':message') : '' }}
                                            </div>
                                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="company_name">Company Name:&emsp;</label>
                                {{ Form::text('company_name', isset($company_name) ? $company_name : '', [
        'class' => 'form-control',
        'placeholder' => 'Company Name'
    ]) }}
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
                                {{ Form::text('shipping_address_street', isset($address) ? $address : '', [
        'class' => 'form-control',
        'placeholder' => 'Address',

    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_street') ? $errors->first('shipping_address_street', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_city">City:&emsp;</label>
                                {{ Form::text('shipping_address_city', isset($city) ? $city : '', [
        'class' => 'form-control',
        'placeholder' => 'City',

    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_city') ? $errors->first('shipping_address_city', ':message') : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_postcode">Postcode:&emsp;</label>
                                {{ Form::text('shipping_address_postcode', isset($zone) ? $zone : '', [
        'class' => 'form-control',
        'placeholder' => 'Postcode',
        'id' => 'shipping_address_postcode'

    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_postcode') ? $errors->first('shipping_address_postcode', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_county">County:&emsp;</label>
                                {{ Form::text('shipping_address_county', isset($county) ? $county : '', [
        'class' => 'form-control',
        'placeholder' => 'County',

    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_county') ? $errors->first('shipping_address_county', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="shipping_address_country">Country:&emsp;</label>
                                {{ Form::text('shipping_address_country', isset($country) ? $country : '', [
        'class' => 'form-control',
        'placeholder' => 'Country',

    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('shipping_address_country') ? $errors->first('shipping_address_country', ':message') : '' }}
                                </div>
                            </div>
                        </div>

                        @if (isset($id))
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="status">Status:&emsp;</label>
                                                {{ Form::select('status', [
            'pending' => 'Pending',
            'booked' => 'Booked',
            'awaiting' => 'Awaiting',
            'canceled' => 'Canceled',
            'failed' => 'Failed',
            'completed' => 'Completed'
        ], isset($status) ? $status : 'pending', [
            'class' => 'form-control',
            'required' => 'required'
        ]) }}
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
                                                            <input type="radio" id="switch_right" name="is_complete" {{ isset($is_complete) && $is_complete == 1 ? 'checked'
            : '' }} value="1" />
                                                            <label for="switch_right">Yes</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="radio_wrap">
                                                                                                            <label class="radio_wrap mr-3">
                                                                                                            <input type="radio" name="is_complete" checked value="0">No
                                                                                                            </label>
                                                                                                            <label class="radio_wrap">
                                                                                                            <input type="radio" name="is_complete" {{ isset($is_complete) && $is_complete == 1 ? 'checked'
                                                                                                            : '' }} value="1">Yes
                                                                                                            </label>
                                                                                                            </div> -->
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
                            {{ Form::text('workshop_date', $defaultDate, [
        'class' => 'form-control',
        'id' => 'created_at',
        'placeholder' => 'Workshop Date',
        'data-date-format' => 'DD-MM-YYYY HH:mm:ss'
    ]) }}
                            <div class="invalid-feedback">
                                {{ $errors->has('workshop_date') ? $errors->first('workshop_date', ':message') : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="box-header with-border ">
                        <h6 class="box-title mb-0">Vehicals Detail</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_reg_number">Vehicle Reg Number
                                    <!-- <span class="text-red">*</span> -->
                                </label>
                                <div class="input-group">
                                    {{ Form::text('vehicle_reg_number', isset($vehicle_reg_number) ? $vehicle_reg_number : '', [
        'class' => 'form-control',
        'placeholder' => 'Vehicle Reg Number',
        'autocapitalize' => 'word',
        'onkeyup' => 'this.value = this.value.toUpperCase()',
        'style' => 'text-transform: uppercase;',
        //'required' => 'required',
        'id' => 'vehicle_reg_number'
    ]) }}
                                    <div class="input-group-append">
            <!-- Lookup Button -->
            <button type="button" id="lookupButton" class="btn btn-primary btn-sm">
                Lookup
            </button>
            <!-- Add Vehicle Button (Hidden by default) -->
            <button type="button" id="addVehicleButton" class="btn btn-primary btn-sm d-none" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="fa fa-plus"></i>
            </button>
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
                                {{ Form::text('vehicle_make', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_make : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_make',
        'placeholder' => 'make Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_make') ? $errors->first('vehicle_make', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_model">Model:&emsp;</label>
                                {{ Form::text('vehicle_model', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_model : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_model',
        'placeholder' => 'model Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_model') ? $errors->first('vehicle_model', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_first_registered">Manufacture Year:&emsp;</label>
                                {{ Form::text('vehicle_first_registered', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->first_registered : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_first_registered',
        'placeholder' => 'First Registered Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_first_registered') ? $errors->first('vehicle_first_registered', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_front_tyre_size">Front Tyre Size:&emsp;</label>
                                {{ Form::text('vehicle_front_tyre_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_front_tyre_size : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_front_tyre_size',
        'placeholder' => 'Front Tyre Size Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_front_tyre_size') ? $errors->first('vehicle_front_tyre_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_rear_tyre_size">Rear Tyre Size:&emsp;</label>
                                {{ Form::text('vehicle_rear_tyre_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_rear_tyre_size : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_rear_tyre_size',
        'placeholder' => 'Rear Tyre Size Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_rear_tyre_size') ? $errors->first('vehicle_rear_tyre_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                       <div class="col-md-3">
    <div class="form-group">
        <label class="control-label" for="vehicle_vin">Vin:&emsp;</label>
        {{ Form::text('vehicle_vin', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_vin : '', [
            'class' => 'form-control',
            'id' => 'vehicle_vin',
            'placeholder' => 'vehicle_VIN Reading'
        ]) }}
        <div class="invalid-feedback">
            {{ $errors->has('vehicle_vin') ? $errors->first('vehicle_vin', ':message') : '' }}
        </div>
    </div>
</div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_cc">Vehicle CC:&emsp;</label>
                                {{ Form::text('vehicle_cc', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_cc : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_cc',
        'placeholder' => 'Vehicle CC Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_cc') ? $errors->first('vehicle_cc', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_engine_number">Engine Number:&emsp;</label>
                                {{ Form::text('vehicle_engine_number', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_engine_number : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_engine_number',
        'placeholder' => 'Engine Number Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_engine_number') ? $errors->first('vehicle_engine_number', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_engine_size">Engine Size:&emsp;</label>
                                {{ Form::text('vehicle_engine_size', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_engine_size : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_engine_size',
        'placeholder' => 'Engine Size Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_engine_size') ? $errors->first('vehicle_engine_size', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_axle">Axle:&emsp;</label>
                                {{ Form::text('vehicle_axle',isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_axle : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_axle',
        'placeholder' => 'vehicle_axle Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_axle') ? $errors->first('vehicle_axle', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_fuel_type">Fuel Type:&emsp;</label>
                                {{ Form::text('vehicle_fuel_type', isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_fuel_type : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_fuel_type',
        'placeholder' => 'Fuel Type Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_fuel_type') ? $errors->first('vehicle_fuel_type', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_mileage">Mileage:&emsp;</label>
                                {{ Form::text('vehicle_mileage',isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() ? $workshopVehicleData->first()->vehicle_mileage : '', [
        'class' => 'form-control ',
        'id' => 'vehicle_mileage',
        'placeholder' => 'Mileage Reading'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_mileage') ? $errors->first('vehicle_mileage', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="vehicle_mot_expiry_date">Mot Expiry Date:&emsp;</label>
                                {{ Form::date('vehicle_mot_expiry_date', 
    isset($workshopVehicleData) && $workshopVehicleData->isNotEmpty() 
        ? $workshopVehicleData->first()->vehicle_mot_expiry_date 
        : null, [
    'class' => 'form-control',
    'id' => 'vehicle_mot_expiry_date',
    'placeholder' => 'Mot Expiry Date Reading'
]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('vehicle_mot_expiry_date') ? $errors->first('vehicle_mot_expiry_date', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        @if (!isset($id))
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="status">Status:&emsp;</label>
                                                {{ Form::select('status', [
            'pending' => 'Pending',
            'booked' => 'Booked',
            'awaiting' => 'Awaiting',
            'canceled' => 'Canceled',
            'failed' => 'Failed',
            'completed' => 'Completed'
        ], isset($status) ? $status : 'pending', [
            'class' => 'form-control',
            'required' => 'required'
        ]) }}
                                                <div class="invalid-feedback">
                                                    {{ $errors->has('status') ? $errors->first('status', ':message') : '' }}
                                                </div>
                                            </div>
                                        </div>
                        @endif

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="payment_method">Payment Method:&emsp;</label>
                                {{ Form::select('payment_method', [
        'pay_at_fitting_center' => 'Pay at Fitting Center',
        'global_payment' => 'Global Payment',
         'dojo' => 'Dojo'
    ], isset($payment_method) ? $payment_method : 'pay_at_fitting_center', [
        'class' => 'form-control',
        'required' => 'required'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('payment_method') ? $errors->first('payment_method', ':message') : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="payment_status">Payment Status:&emsp;</label>
                                {{ Form::select('payment_status', [
        '1' => 'Paid',
        '0' => 'Unpaid',
        '3' => 'Partially'
    ], isset($payment_status) ? $payment_status : '0', [
        'class' => 'form-control',
        'required' => 'required'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('payment_status') ? $errors->first('payment_status', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="fitting_type">Fitting Type:&emsp;</label>
                                <select id="fitting_type" name="fitting_type" class="form-control" required>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                <div class="invalid-feedback">
                                    {{ $errors->has('fitting_type') ? $errors->first('fitting_type', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="due_in">Due In<span class="text-red">*</span></label>
                                {{ Form::input('datetime-local', 'due_in', isset($due_in) ?
        \Carbon\Carbon::parse($due_in)->format('Y-m-d\TH:i') :
        \Carbon\Carbon::now('Europe/London')->format('Y-m-d\TH:i'), [
        'class' => 'form-control',
        'placeholder' => 'Due In',
        'id' => 'due_in',
        'required'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('due_in') ? $errors->first('due_in', ':message') : '' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="due_out">Due Out<span class="text-red">*</span></label>
                                {{ Form::input('datetime-local', 'due_out', isset($due_out) ?
        \Carbon\Carbon::parse($due_out)->format('Y-m-d\TH:i') :
        \Carbon\Carbon::now('Europe/London')->format('Y-m-d\TH:i'), [
        'class' => 'form-control',
        'placeholder' => 'Due Out',
        'id' => 'due_out',
        'required'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('due_out') ? $errors->first('due_out', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="advisor">Notes:&emsp;</label>
                                {{ Form::textarea('notes', isset($notes) ? $notes : '', [
        'class' => 'form-control ',
        'placeholder' => 'Notes',
        'style' => 'height:50px'
    ]) }}
                                <div class="invalid-feedback">
                                    {{ $errors->has('notes') ? $errors->first('notes', ':message') : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row text-center mb-4">
                <div class="col-6"><button id="addTyreButton" class="btn btn-primary btn-block" type="button">Add Tyre</button>
                </div>
            </button>
                <div class="col-6"><button id="addServiceButton" class="btn btn-primary btn-block" type="button">Add
                        Service</button></div>
            </div>

            <!-- tyre section start -->
            <div class="card">
                <h6 class="card-header">Tyres</h6>
                <!-- Include Modal for Tyre Selection -->
                @include('AutoCare/workshop/tyre-modal')
                @include('AutoCare/workshop/service-modal')
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <th style="white-space: nowrap">Item &emsp;&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Description &emsp;&emsp;</th>
                            <th style="white-space: nowrap">Quantity&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Cost Price&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Rate&emsp;&emsp;</th>
                            <th style="white-space: nowrap">Vat &emsp;&emsp;</th>
                            <!-- <th style="white-space: nowrap">Status&emsp;&emsp;&emsp;</th> -->
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
            $quantity = $value->quantity ?? 1; // Default to 1 if not set
            $tax_class_id = $value->tax_class_id ?? 0;
            $vatRate = ($tax_class_id == 9) ? 0.2 : 0; // 20% VAT if tax_class_id is 9, otherwise 0%
            $price = $value->cost_price ?? 0; // Default to 0 if not set
            $rate = $value->margin_rate ?? 0; // Default to 0
            $subtotal = $rate * $quantity; // Subtotal without VAT
            $vatAmount = $subtotal * $vatRate; // VAT amount
            $totalAmount = $subtotal + $vatAmount; // Total with VAT
                            @endphp

                            <tr id="AddRowForProduct{{ $incrementedId }}">
                            <input type="hidden" name="item_id[]" value="{{ $value->id }}" required>
                            <input type="hidden" name="product_id[]" value="{{ $value->product_id }}" required>
                            <input type="hidden" name="tyre_ean[]" value="{{ $value->product_ean }}">
                            <input type="hidden" name="tyre_sku[]" value="{{ $value->product_sku }}">
                            <input type="hidden" name="tyre_supplier_name" value="{{ $value->supplier }}">
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
                                <input type="number" name="cost_price[]" value="{{ number_format($price, 2) }}"
                                    class="form-control cost-price" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="margin_rate[]" value="{{ number_format($rate, 2) }}"
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
                                    <td colspan="8">Please Add Tyre</td>
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
                                <th>Quantity</th>
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
            $quantity = $value->service_quantity ?? 1; // Default to 1 if not set
            $tax_class_id = $value->tax_class_id ?? 0;
            $vatRate = $tax_class_id == 9 ? 0.2 : 0; // 20% VAT if tax_class_id is 9, otherwise 0%
            $price = $value->service_price ?? 0; // Default to 0 if not set
            $subtotal = $price * $quantity; // Subtotal without VAT
            $vatAmount = $subtotal * $vatRate; // VAT amount
            $totalAmount = $subtotal + $vatAmount; // Total including VAT
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
                                    <td colspan="6">Please Add a Service</td> <!-- Adjusted colspan -->
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <div class="row">
                    <div class="col-sm-3">
                        Total Tyre Price : <b id="total_Product_amount"></b>
                        <input type="hidden" name="total_tyre_price" id="total_tyre_price_input">
                    </div>
                    <div class="col-sm-3">
                    <div class="" id="calloutChargesSection" style="display: none;">
                        Callout Charges: <b id="calloutCharges"></b><br>
                        <input type="hidden" name="callout_charges" id="callout_charges_input">
                        <input type="hidden" name="callout_vat" id="callout_vat_input">
                         <input type="hidden" name="callout_postcode" id="callout_postcode_input">
                        <input type="hidden" name="total_callout" id="total_callout_input">
                    </div>
                    </div>
                    <div class="col-sm-3">
                        Total Service Price : <b id="total_Service_amount"></b>
                        <input type="hidden" name="total_service_price" id="total_service_price_input">
                    </div>
                    <div class="col-sm-3">
                        Grand Total : <b id="total_grand_amount"></b>
                        <input type="hidden" name="grand_total" id="grand_total_input">
                    </div>
                </div>
            </div>
            <!-- tyre section end -->
            @php
    $incrimentedId_es = 0;
            @endphp
            @if (!isset($id))
            @endif
            <div class="col-md-12 text-center mt-3 mb-3">
    <button type="submit" class="btn btn-sm btn-primary" name="save_only"> 
        <i class="fa fa-dot-circle-o"></i> {{ isset($id) ? 'Update' : 'Add' }}
    </button>
    <button type="submit" class="btn btn-sm btn-success" name="save_and_sync_invoice"> 
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

                        <!-- Error Message -->
                        <div class="mt-2">
                            <span id="postcodeErrorMsg" class="text-danger"></span>
                        </div>

                        <!-- Result Message -->
                        <div id="resultMessage" class="mt-3"></div>

                        <!-- Action Buttons -->
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
        @include('AutoCare/workshop/modal/add_vehicle')
        @include('AutoCare/workshop/modal/vrm_customer')
        @include('AutoCare/workshop/modal/add_tyre')
        @endif
         @if(isset($workshopTyreData) && $workshopTyreData->isNotEmpty())
                    @php
                        $firstTyre = $workshopTyreData->first();
                        $fitting_type = $firstTyre->fitting_type ?? 'fully_fitted';
                        $shipping_postcode = $firstTyre->shipping_postcode ?? '';
                        $callout_charges = $firstTyre->shipping_price ?? 0;
                        $callout_vat = $firstTyre->shipping_tax_id ?? 0;
                        $total_callout = $firstTyre->shipping_price *1.2 ?? 0;
                    @endphp
                    <script>
                        $(document).ready(function() {
                            // Set fitting type
                            $('#fitting_type').val('{{ $fitting_type }}');

                            // If mobile fitted, show callout charges section and populate values
                            if ('{{ $fitting_type }}' === 'mobile_fitted') {
                                $('#calloutChargesSection').show();
                                $('#calloutCharges').text('£{{ number_format($total_callout, 2) }}');
                                $('#callout_charges_input').val('{{ $callout_charges }}');
                                $('#callout_vat_input').val('{{ $callout_vat }}');
                                $('#callout_postcode_input').val('{{ $shipping_postcode }}');
                                $('#total_callout_input').val('{{ $total_callout }}');

                                // Store postcode data for total calculations
                                window.postcodeResponseData = {
                                    postcode: '{{ $shipping_postcode }}',
                                    ship_price: {{ $callout_charges }},
                                    includes_vat: {{ $callout_vat }},
                                    total_price: {{ $total_callout }}
                                };

                                // Update shipping address postcode
                                $('#shipping_address_postcode').val('{{ $shipping_postcode }}');
                            }

                            // Update totals to include callout charges
                            updateTotals();
                        });
                    </script>
                @endif
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fittingTypeDropdown = document.getElementById('fitting_type');
                const postcodeModal = new bootstrap.Modal(document.getElementById('postcodeModal'), {
                    backdrop: true,
                    keyboard: true,
                });

                // Show modal when "mobile_fitted" is selected
                fittingTypeDropdown.addEventListener('change', function () {
                    if (this.value === 'mobile_fitted') {
                        postcodeModal.show();
                    } else {
                        // Hide callout charges section if not mobile fitted
                        document.getElementById('calloutChargesSection').style.display = 'none';
                    }
                });

                // Handle postcode form submission
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

                    // Clear previous messages
                    errorMsg.textContent = '';
                    resultDiv.innerHTML = '';

                    // Send postcode to server
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
                                // Store response data temporarily
                                window.postcodeResponseData = data;

                                // Display result in modal
                                resultDiv.innerHTML = `
                                        Postcode: ${data.postcode}<br>
                                        Distance: ${data.distance_in_miles} Miles<br>
                                        Total Price: £${data.total_price}
                                    `;

                                // Show action buttons
                                continueButton.style.display = 'inline-block';
                                changePostcodeButton.style.display = 'inline-block';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            errorMsg.textContent = error.message || 'An unexpected error occurred.';
                        });
                });

                // Handle "Change Postcode" button click
                document.getElementById('changePostcodeButton').addEventListener('click', function () {
                    document.getElementById('postcode').value = '';
                    document.getElementById('resultMessage').innerHTML = '';
                    document.getElementById('continueButton').style.display = 'none';
                    document.getElementById('changePostcodeButton').style.display = 'none';
                });

                // Handle "Continue" button click
                document.getElementById('continueButton').addEventListener('click', function () {
                    const data = window.postcodeResponseData;

                    // Update shipping address postcode
                    document.getElementById('shipping_address_postcode').value = data.postcode;

                    // Show callout charges section
                    const calloutChargesSection = document.getElementById('calloutChargesSection');
                    calloutChargesSection.style.display = 'block';
                   document.getElementById('calloutCharges').textContent = `£${data.total_price.toFixed(2)}`;
                    document.getElementById('callout_charges_input').value = data.ship_price;
                    document.getElementById('callout_vat_input').value = data.includes_vat; // 20% VAT
                    document.getElementById('callout_postcode_input').value = data.postcode;
                    document.getElementById('total_callout_input').value = data.total_price;

                    // Store data in session
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
                                updateTotals(); // Update totals with new shipping price
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
        <style>
            .text-red {
                color: red;
                font-size: 15px;
            }
        </style>
        <script>
            $(document).ready(function () {
                // Function to validate stock availability and update max attribute
                async function validateStock(productId, row) {
                    try {
                        const response = await fetch(`/validate-tyre-stock/${productId}`);
                        const data = await response.json();

                        if (!data.success) {
                            // Display error message and disable the submit button
                            row.find('.stock-error').text(`Insufficient stock! Available: ${data.available}`);
                            row.addClass('error-row'); // Highlight the row with an error
                            $('#submitButton').prop('disabled', true); // Disable the submit button
                            return false;
                        } else {
                            // Update the max attribute of the quantity input field
                            const quantityInput = row.find('input[name="tyre_quantity[]"]');
                            const currentQuantity = parseInt(quantityInput.val()) || 1;
                            const availableStock = data.available;

                            // Dynamically set the max attribute to the available stock
                            // quantityInput.attr('max', availableStock);

                            // If the current quantity exceeds the available stock, reset it
                            // if (currentQuantity > availableStock) {
                            //     quantityInput.val(availableStock);
                            //     row.find('.stock-error').text(`Available stock is ${availableStock}. Cannot exceed this value.`);

                            //     // Delay clearing the error message
                            //     setTimeout(() => {
                            //         row.find('.stock-error').text('');
                            //     }, 5000); // Clear the error message after 5 seconds
                            // } else {
                            //     row.find('.stock-error').text(''); // Clear error message
                            // }

                            // Clear error highlighting and enable the submit button
                            row.removeClass('error-row');
                            if ($('#tBodyForProductTable .error-row').length === 0) {
                                $('#submitButton').prop('disabled', false);
                            }
                            return true;
                        }
                    } catch (error) {
                        console.error('Error validating stock:', error);
                        row.find('.stock-error').text('An error occurred while validating stock.');
                        row.addClass('error-row');
                        $('#submitButton').prop('disabled', true);
                        return false;
                    }
                }

                // Attach event listeners for tyre selection
                $(document).on('change', 'input[name="product_id[]"]', function () {
                    const row = $(this).closest('tr');
                    const productId = $(this).val();

                    if (productId) {
                        validateStock(productId, row);
                    }
                });

                // Trigger stock validation on page load for pre-filled rows
                $('#tBodyForProductTable tr').each(function () {
                    const row = $(this);
                    const productId = row.find('input[name="product_id[]"]').val();

                    if (productId) {
                        validateStock(productId, row);
                    }
                });

                // Prevent manual input beyond the max attribute
                // $(document).on('input', 'input[name="tyre_quantity[]"]', function () {
                //     const row = $(this).closest('tr');
                //     const max = parseInt($(this).attr('max')) || 1;
                //     let value = parseInt($(this).val()) || 1;

                //     // Ensure the value does not exceed the max attribute
                //     if (value > max) {
                //         $(this).val(max);
                //         row.find('.stock-error').text(`Available stock is ${max}. Cannot exceed this value.`);

                //         // Delay clearing the error message
                //         setTimeout(() => {
                //             row.find('.stock-error').text('');
                //         }, 5000); // Clear the error message after 5 seconds
                //     } else {
                //         row.find('.stock-error').text(''); // Clear error message
                //     }

                //     // Trigger stock validation if needed
                //     const productId = row.find('input[name="product_id[]"]').val();
                //     if (productId) {
                //         validateStock(productId, row);
                //     }
                // });
            });
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
                // setInterval(function () {
                $('[name^=product_price_es]').removeAttr("min");
                $('[name^=product_quantity_es]').removeAttr("max");
                var TotalProduct = 0;
                var total_service_amount = 0;
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
                            total_service_amount += parseFloat($(this).val()) * parseFloat($(this).parent().parent()
                                .find('[name^=service_quantity]').val());
                        }
                        return parseFloat($(this).val());

                    }).get();
                if (!isNaN(TotalProduct)) {
                    $('[id=total_Product_amount]').html(TotalProduct);
                }
                if (!isNaN(total_service_amount)) {
                    $('[id=total_service_amount]').html(total_service_amount);
                }
                total_grand_amount += total_service_amount + TotalProduct;
                if (!isNaN(total_grand_amount)) {
                    $('[id=total_grand_amount]').html(total_grand_amount);
                }
                // }, 1000)


                // $('[name^=customer_id]').selectize({
                //      create: false,
                //      sortField: 'text'
                //    });

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

                $(document).on("change",'#registered_vehicle', function () {
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
                            console.log(data)

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

                                // Populate the vehicle dropdown
                                $('#registered_vehicle').empty()
                                    .append('<option value="">- Select Vehicle -</option>');
                                vehicleRegNum.forEach(vehicle => {
                                    $('#registered_vehicle').append(`<option value="${vehicle.vehicle_reg_number}">${vehicle.vehicle_reg_number}</option>`);
                                });

                                // Show the vehicle dropdown
                                $('#registered_vehicleHS').show();

                                // Select the first option by default
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
                let serviceData = []; // Store fetched service data globally

                // Fetch and Display Services in Modal
                $('#addServiceButton').click(function () {
                    $('#serviceModal').modal('show');
                    if (!serviceData.length) {
                        fetchServices(); // Fetch services if not already loaded
                    }
                });

                function fetchServices() {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/') }}/AutoCare/get-services", // Ensure the correct API route
                        success: function (response) {
                            // console.log(response);
                            serviceData = response.services || [];
                            populateServiceModal(serviceData);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching services:', error);
                        }
                    });
                }

                function populateServiceModal(services) {
                    const serviceList = $('#serviceList');
                    serviceList.empty();

                    if (!Array.isArray(services) || services.length === 0) {
                        serviceList.append('<tr><td colspan="3">No services available</td></tr>');
                        return;
                    }

                    services.forEach(service => {
                        serviceList.append(`

                        <label class="table_box">
                        <div class="item servicename">${service.name}</div>
                        <div class="item cost">£${parseFloat(service.cost_price).toFixed(2)}</div>
                        <div class="item check"><input type="checkbox" class="service-checkbox" 
                        data-id="${service.service_id}" 
                        data-name="${service.name}"
                        data-vat="${service.tax_class_id}" 
                        data-price="${service.cost_price}"></div>
                        </label>
                        `);
                    });
                }

                // Add Selected Services to Table on Click
                $('#addSelectedServices').click(function () {
                    let selectedServices = [];

                    $('.service-checkbox:checked').each(function () {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let price = parseFloat($(this).data('price')) || 0;
                        let vatClass = $(this).data('vat');

                        // Determine VAT rate based on tax_class_id
                        let vatRate = (vatClass === 9) ? 0.2 : 0.0; // 20% for tax_class_id 9, 0% for tax_class_id 0
                        let vatAmount = price * vatRate;
                        let total = price + vatAmount;

                        selectedServices.push(`
                        <tr>
                        <input type="hidden" name="service_id[]" value="${id}">
                            <td><input type="hidden" name="service_name[]" value="${name}">${name}</td>
                            <td><input type="number" name="service_quantity[]" class="form-control quantity" value="1" min="1" data-price="${price}"></td>
                            <td><input type="number" step="0.01"name="service_price[]" class="form-control price" value="${price.toFixed(2)}"></td>
                                <td>
                                <input type="hidden" name="service_vat[]" value="${vatClass}">
                                <select name="vat_rate[]" class="form-control vat-rate">
                                    <option value="0" ${vatClass === 0 ? 'selected' : ''}>0% VAT</option>
                                    <option value="0.2" ${vatClass === 9 ? 'selected' : ''}>20% VAT</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="service_total[]" class="form-control service-total" step="0.01" value="${total.toFixed(2)}"></td>
                            <td><button type="button" class="btn btn-danger remove-service">Remove</button></td>
                        </tr>
                    `);
                    });

                    $('#selectedServicesTable tbody').append(selectedServices);
                    $('#serviceModal').modal('hide');
                    updateServiceTotals();
                });

                // Update VAT & Total When VAT Rate is Changed
                $(document).on('change', '.vat-rate, .quantity, .price', function () {
                    let row = $(this).closest('tr');
                    let quantity = parseFloat(row.find('.quantity').val()) || 1;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let vatRate = parseFloat(row.find('.vat-rate').val());

                    let subtotal = price * quantity;
                    let vatAmount = subtotal * vatRate;
                    let total = subtotal + vatAmount;

                    row.find('.service-vat').val(vatAmount.toFixed(2));
                    row.find('.service-total').val(total.toFixed(2));

                    updateServiceTotals();
                    updateTotals();     
                });


                // Update VAT & Total When Quantity or Price Changes
                $(document).on('input', '.quantity, .price', function () {
                    let row = $(this).closest('tr');
                    let quantity = parseFloat(row.find('.quantity').val()) || 1;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let vatRate = parseFloat(row.find('.vat-rate').val());

                    let subtotal = price * quantity;
                    let vatAmount = subtotal * vatRate;
                    let total = subtotal + vatAmount;

                    row.find('.service-vat').val(vatAmount.toFixed(2));
                    row.find('.service-total').val(total.toFixed(2));

                    updateServiceTotals();
                });

                // Remove Service Row
                $(document).on('click', '.remove-service', function () {
                    $(this).closest('tr').remove();
                    updateServiceTotals();
                });

                // Update Grand Total of Selected Services
                function updateServiceTotals() {
                    let grandTotal = 0;

                    $('#selectedServicesTable tbody tr').each(function () {
                        let total = parseFloat($(this).find('.service-total').val()) || 0;
                        grandTotal += total;
                    });

                    $('#total_Service_amount').text(`£${grandTotal.toFixed(2)}`);

                    updateTotals(); // Ensure grand total updates
                }

            });
            // Update Totals (Product price and Grand Total)
            function updateTotals() {
                let totalProductPrice = 0;
                let totalServicePrice = 0;
                let shippingPrice = 0;
                let shippingVat = 0;

                // Calculate total for tyres
                $('#tBodyForProductTable tr').each(function () {
                    const quantity = parseFloat($(this).find('input[name="tyre_quantity[]"]').val()) || 1;
                    const rate = parseFloat($(this).find('input[name="margin_rate[]"]').val()) || 0;
                    const vatType = parseFloat($(this).find('select[name="tyre_vat[]"]').val()) || 0;

                    // Calculate amount for tyres
                    const amount = (rate * quantity) * (1 + (vatType === 9 ? 0.2 : 0));
                    $(this).find('input[name="tyre_amount[]"]').val(amount.toFixed(2));

                    totalProductPrice += amount;
                });

                // Calculate total for services
                $('#selectedServicesTable tbody tr').each(function () {
                    const serviceTotal = parseFloat($(this).find('.service-total').val()) || 0;
                    totalServicePrice += serviceTotal;
                });
                // Add shipping price if fitting type is mobile_fitted
                if ($('#fitting_type').val() === 'mobile_fitted' || window.postcodeResponseData) {
                    shippingPrice = parseFloat(window.postcodeResponseData.ship_price) || 0;
                    shippingVat = shippingPrice * 0.2; // 20% VAT on shipping
                }
                // shippingPrice = parseFloat(window.postcodeResponseData.ship_price) || 0;
                // shippingVat = shippingPrice * 0.2; // 20% VAT on shipping

                const grandTotal = totalProductPrice + totalServicePrice + shippingPrice + shippingVat;

                // Update display values
                $('#total_Product_amount').text(totalProductPrice.toFixed(2));
                $('#total_Service_amount').text(totalServicePrice.toFixed(2));
                if (shippingPrice > 0) {
                    $('#shipping_price').text(shippingPrice.toFixed(2));
                    $('#shipping_vat').text(shippingVat.toFixed(2));
                }
                $('#total_grand_amount').text(grandTotal.toFixed(2));
                // Update hidden input fields
                $('#total_tyre_price_input').val(totalProductPrice.toFixed(2));
                $('#total_service_price_input').val(totalServicePrice.toFixed(2));
                $('#grand_total_input').val(grandTotal.toFixed(2));
            }
        </script>
        <script>
            $(document).ready(function () {
    $('#customer_id').select2({
        placeholder: 'Search by Name or Company',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '/customers/search', // Replace with your API endpoint
            dataType: 'json',
            delay: 250, // Delay in milliseconds before sending the request
            data: function (params) {
                return {
                    q: params.term, // Search term entered by the user
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text, // Display text combining customer_name and company_name
                        };
                    }),
                };
            },
            cache: true,
        },
        minimumInputLength: 2, // Minimum characters required to trigger the search
    });
});
            </script>
        <script>
            $(document).ready(function () {
                let tyreProducts = []; // Store fetched tyre data globally
                let currentFilters = {}; // Store current filter values globally

                // Fetch Tyre Products and Populate Filters
                $('#addTyreButton').click(function () {
                    const selectedFittingType = $('#fitting_type').val();
                    $('#tyreModal').modal('show');
                    if (selectedFittingType) {
                        $('#fittingtype').val(selectedFittingType).trigger('change');
                        applyFilters();
                    }
                });

                function populateSupplierFilter() {
                const supplierDropdown = $('#supplier');
                if (supplierDropdown.children().length > 1) return; // Prevent redundant population

                $.ajax({
                    type: "GET",
                    url: "{{ route('AutoCare.workshop.getSuppliers') }}",
                    success: function (response) {
                        const suppliers = response.suppliers;
                        if (Array.isArray(suppliers)) {
                            supplierDropdown.empty(); // Clear existing options
                            supplierDropdown.append('<option value="">-- Select Supplier --</option>'); // Add default placeholder

                            suppliers.forEach((supplier) => {
                                const option = `<option value="${supplier.supplier_name}" ${
                                    supplier.supplier_name === 'ownstock' ? 'selected' : ''
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

                    const prevButton = $(`<button type="button" class="btn btn-sm mx-1 ${current_page === 1 ? 'btn-secondary' : 'btn-primary'}" ${current_page === 1 ? 'disabled' : ''}>Previous</button>`);
                    prevButton.click((e) => {
                        e.preventDefault();
                        fetchTyreProducts(current_page - 1);
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
                            fetchTyreProducts(i);
                        });
                        paginationControls.append(pageButton);
                    }

                    const nextButton = $(`<button type="button" class="btn btn-sm mx-1 ${current_page === last_page ? 'btn-secondary' : 'btn-primary'}" ${current_page === last_page ? 'disabled' : ''}>Next</button>`);
                    nextButton.click((e) => {
                        e.preventDefault();
                        fetchTyreProducts(current_page + 1);
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
                        <td>${
                        product.tax_class_id === 9
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
                            <input type="hidden" name="tyre_supplier_name" value="${selectedProduct.tyre_supplier_name}">
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
                            <input type="number" step="0.01" name="cost_price[]"  value="${tyre_price.toFixed(2)}" class="form-control" required>
                            </td>
                            <td>
                            <input type="number" step="0.01" name="margin_rate[]"  value="${rate.toFixed(2)}" class="form-control">
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
                            $('#tBodyForProductTable').append(newRow);
                        }

                        $('#tyreModal').modal('hide');
                        updateTotals();
                    });
                }

                $(document).on('input', 'input[name="tyre_quantity[]"],input[name="cost_price[]"], input[name="margin_rate[]"], input[name="tyre_vat[]"]', function () {
                    updateTotals();
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

                        // Populate vehicle details fields
                        document.getElementById('vehicle_make').value = vehicleDetails.DvlaMake || '';
                        document.getElementById('vehicle_model').value = vehicleDetails.DvlaModel || '';
                        document.getElementById('vehicle_vin').value = vehicleDetails.VinLast5 || '';
                        document.getElementById('vehicle_first_registered').value = vehicleDetails.YearOfManufacture || '';

                        document.getElementById('vehicle_engine_size').value = SmmtDetails.EngineCapacityCc || '';
                        document.getElementById('vehicle_axle').value = SmmtDetails.DriveType || '';
                        document.getElementById('vehicle_cc').value = SmmtDetails.EngineCapacityCc || '';
                        document.getElementById('vehicle_fuel_type').value = SmmtDetails.FuelType || '';
                        document.getElementById('vehicle_engine_number').value = vehicleDetails.EngineNumber || '';
                        // Populate tyre details fields
                        document.getElementById('vehicle_front_tyre_size').value = tyreDetails.Front?.Tyre?.SizeDescription || '';
                        document.getElementById('vehicle_rear_tyre_size').value = tyreDetails.Rear?.Tyre?.SizeDescription || '';
                        // Populate MOT history fields
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
                function populateFittingType(selectedValue = 'fully_fitted') { // Default to 'fully_fitted'
                    $.ajax({
                        type: "GET",
                        url: "{{ route('AutoCare.workshop.getOrderType') }}", // Replace with your order type API endpoint
                        success: function (response) {
                            // console.log(response);

                            // Check if the response contains the 'ordertype_name' key and it's an array
                            const orderTypes = response.ordertype_name;

                            if (Array.isArray(orderTypes)) {
                                // Clear existing options
                                fittingTypeDropdown.empty();

                                // Populate dropdown with order type options
                                orderTypes.forEach((orderType) => {
                                    const isSelected = orderType.ordertype_name === selectedValue ? 'selected' : '';
                                    fittingTypeDropdown.append(`
                                                                                <option value="${orderType.ordertype_name}" ${isSelected}>
                                                                                    ${orderType.ordertype_name.replace(/_/g, ' ').toUpperCase()}
                                                                                </option>
                                                                            `);
                                });

                                // Ensure "Fully Fitted" is selected by default
                                if (!fittingTypeDropdown.find('option:selected').length) {
                                    fittingTypeDropdown.val('fully_fitted'); // Fallback to "fully_fitted"
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
                // Call the function to populate the dropdown
                const fittingTypeFromBackend = '{{ isset($fitting_type) ? $fitting_type : null }}';
                populateFittingType(fittingTypeFromBackend || 'fully_fitted'); // Default to 'fully_fitted' if no value from backend
            });
        </script>

