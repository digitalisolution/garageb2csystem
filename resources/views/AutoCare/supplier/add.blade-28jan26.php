@extends('samples')
@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">
            <!-- Supplier Details Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" href="#tab-csv" data-toggle="tab"><strong>CSV Detail</strong></a></li>
                            @if (isset($id) ? $id : '')
                            <li class="nav-item"><a class="nav-link" href="#delivery_time" data-toggle="tab"><strong>Delivery Time</strong></a></li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active in" id="tab-csv">
                                {{ Form::open(['url' => 'AutoCare/supplier/add', 'files' => true, 'enctype' => 'multipart/form-data', 'autocomplete' => 'OFF']) }}
                                {{ csrf_field() }}
                                {{ Form::hidden('id', isset($id) ? $id : '', []) }}
                                <div class="box-header with-border">
                                    <h5>Please Fill Up Supplier Details</h5>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        @if ($errors->any())
                                            <ul class="alert alert-danger" style="list-style:none">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if(session()->has('message.level'))
                                            <div class="alert alert-{{ session('message.level') }} alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!
                                                </h4>
                                                {!! session('message.content') !!}
                                            </div>
                                        @endif
                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label for="garage_id">Select Garage</label>
                                            <select name="garage_id" class="form-control" required>
                                                <option value="">-- Select Garage --</option>
                                                @foreach($garages as $garage)
                                                    <option value="{{ $garage->id }}"
                                                        {{ old('garage_id', $garage_id ?? '') == $garage->id ? 'selected' : '' }}>
                                                        {{ $garage->garage_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        
                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="supplier_name">Supplier Name</label>
                                            {{ Form::text('supplier_name', isset($supplier_name) ? $supplier_name : '', ['class' => 'form-control', 'id' => 'supplier_name', 'required', 'placeholder' => 'Supplier Name']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="mob_num">Contact Number</label>
                                            {{ Form::number('mob_num', isset($mob_num) ? $mob_num : '', ['class' => 'form-control', 'placeholder' => 'Mobile Number']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="email">Email</label>
                                            {{ Form::text('email', isset($email) ? $email : '', ['class' => 'form-control', 'placeholder' => 'Email']) }}
                                        </div>

                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label class="form-col-form-label" for="gstin">VAT No.</label>
                                            {{ Form::text('gstin', isset($gstin) ? $gstin : '', ['class' => 'form-control', 'placeholder' => 'VAT NO.']) }}
                                        </div>

                                        <!-- Import Options -->
                                        <div class="col-lg-3 col-md-6 col-12 form-group">
                                            <label for="import_method">Import Method</label>
                                            {{ Form::select('import_method', ['csv' => 'Upload CSV', 'ftp' => 'Fetch from FTP', 'file_path' => 'Import from File Path'], isset($import_method) ? $import_method : '', ['class' => 'form-control', 'id' => 'import_method', 'placeholder' => 'Select Import Method']) }}
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-12 form-group">
                                            <label class="form-col-form-label" for="address">Address</label>
                                            {{ Form::textarea('address', isset($address) ? $address : '', ['class' => 'form-control', 'style' => 'height:75px', 'placeholder' => 'Address']) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- File Upload for CSV -->
                                        <div id="csv-upload" class="col-lg-3 col-md-6 col-12 import-option"
                                            style="display:none;">
                                            <div class="form-group">
                                                <label for="csv_file">Upload Supplier CSV</label>
                                                {{ Form::file('csv_file', ['class' => 'form-control', 'accept' => '.csv']) }}
                                            </div>
                                        </div>
                                        <!-- FTP Details -->
                                        <div class="col-lg-12">
                                            <div id="ftp-details" class="import-option" style="display:none;">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="ftp_host">FTP Host</label>
                                                        {{ Form::text('ftp_host', isset($ftp_host) ? $ftp_host : '', ['class' => 'form-control', 'id' => 'ftp_host', 'placeholder' => 'Enter FTP Host']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="ftp_user">FTP Username</label>
                                                        {{ Form::text('ftp_user', isset($ftp_user) ? $ftp_user : '', ['class' => 'form-control', 'id' => 'ftp_user', 'placeholder' => 'Enter FTP Username']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label for="ftp_password">FTP Password</label>
                                                        {{ Form::password('ftp_password', ['class' => 'form-control', 'id' => 'ftp_password', 'placeholder' => 'Enter FTP Password']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label for="ftp_directory">FTP Directory</label>
                                                        {{ Form::text('ftp_directory', isset($ftp_directory) ? $ftp_directory : '', ['class' => 'form-control', 'id' => 'ftp_directory', 'placeholder' => 'Enter FTP Directory']) }}
                                                    </div>
                                                    <div class="col-lg-2 col-md-6 col-12 form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-primary"
                                                            id="testConnectionButton">Test
                                                            Connection</button>
                                                        <p id="connectionStatusMessage" style="color: green;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- File Path Details -->
                                        <div id="file-path" class="col-lg-3 col-md-6 col-12 import-option"
                                            style="display:none;">
                                            <div class="form-group">
                                                <label for="file_path">File Path</label>
                                                {{ Form::text('file_path', isset($file_path) ? $file_path : '', ['class' => 'form-control', 'placeholder' => 'Enter File Path']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- API Order Enable (1 or 0) -->
                                        <div class="col-12">
                                            <h5 class="mt-3">API Order Details</h5>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-12 form-group">
                                            <label for="api_order_enable">API Order Enable</label>
                                            {{ Form::select('api_order_enable', ['0' => 'Disabled','1' => 'Enabled'], isset($api_order_enable) ? $api_order_enable : (isset($api_order_details['api_order_enable']) ? $api_order_details['api_order_enable'] : ''), ['class' => 'form-control','id' => 'api_order_enable']) }}
                                        </div>

                                        <!-- Status Field -->
                                        <div class="col-lg-6 col-md-6 col-12 form-group">
                                            <label for="status">Status</label>
                                            {{ Form::select('status', ['1' => 'Active', '0' => 'Inactive'], isset($status) ? $status : '', ['class' => 'form-control']) }}
                                        </div>
                                        @if(isset($supplier_name) && $supplier_name == 'bond')
                                        <div class="col-lg-12">
                                            <div id="api_order_details"
                                                style="{{ isset($api_order_details['api_order_enable']) && $api_order_details['api_order_enable'] == '1' ? 'display: block;' : 'display: none;' }}">
                                                <div class="row">
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_supplieremail">Supplier Email</label>
                                                        {{ Form::text('api_order_details[bond_supplieremail]', isset($api_order_details['bond_supplieremail']) ? $api_order_details['bond_supplieremail'] : '', ['class' => 'form-control', 'placeholder' => 'Supplier Email']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_api_mode">API Mode</label>
                                                        {{ Form::select('api_order_details[bond_api_mode]', ['live' => 'Live', 'test' => 'Test'], isset($api_order_details['bond_api_mode']) ? $api_order_details['bond_api_mode'] : '', ['class' => 'form-control', 'placeholder' => 'Select Bond Api Mode']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_api_code">API Code</label>
                                                        {{ Form::text('api_order_details[bond_api_code]', isset($api_order_details['bond_api_code']) ? $api_order_details['bond_api_code'] : '', ['class' => 'form-control', 'placeholder' => 'API Code']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="trading_point">Trading Point</label>
                                                        {{ Form::text('api_order_details[trading_point]', isset($api_order_details['trading_point']) ? $api_order_details['trading_point'] : '', ['class' => 'form-control', 'placeholder' => 'Trading Point']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bond_status_autoorder">Auto Order Status</label>
                                                        {{ Form::select('api_order_details[bond_status_autoorder]', ['1' => 'Enabled', '0' => 'Disabled'], isset($api_order_details['bond_status_autoorder']) ? $api_order_details['bond_status_autoorder'] : '', ['class' => 'form-control', 'placeholder' => 'Select Auto Order Status']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @elseif(isset($supplier_name) && $supplier_name=='bmtr')
                                        <div class="col-lg-12">
                                            <div id="api_order_details"
                                                style="{{ isset($api_order_details['api_order_enable']) && $api_order_details['api_order_enable'] == '1' ? 'display: block;' : 'display: none;' }}">
                                                <div class="row">                                                   
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_api_mode">BMTR API Mode</label>
                                                        {{ Form::select('api_order_details[bmtr_api_mode]', ['live' => 'Live', 'test' => 'Test'], isset($api_order_details['bmtr_api_mode']) ? $api_order_details['bmtr_api_mode'] : '', ['class' => 'form-control', 'placeholder' => 'Select BMTR API Mode']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_siteid">Site Code</label>
                                                        {{ Form::text('api_order_details[bmtr_siteid]', isset($api_order_details['bmtr_siteid']) ? $api_order_details['bmtr_siteid'] : '', ['class' => 'form-control', 'placeholder' => 'Site Code']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_api_username">API User</label>
                                                        {{ Form::text('api_order_details[bmtr_api_username]', isset($api_order_details['bmtr_api_username']) ? $api_order_details['bmtr_api_username'] : '', ['class' => 'form-control', 'placeholder' => 'API Username']) }}
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_api_password">API Password</label>
                                                        {{ Form::text('api_order_details[bmtr_api_password]', isset($api_order_details['bmtr_api_password']) ? $api_order_details['bmtr_api_password'] : '', ['class' => 'form-control', 'placeholder' => 'API Password']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_api_key">API Key</label>
                                                        {{ Form::text('api_order_details[bmtr_api_key]', isset($api_order_details['bmtr_api_key']) ? $api_order_details['bmtr_api_key'] : '', ['class' => 'form-control', 'placeholder' => 'API Key']) }}
                                                    </div>

                                                    <div class="col-lg-3 col-md-6 col-12 form-group">
                                                        <label for="bmtr_status_autoorder">Auto Order Status</label>
                                                        {{ Form::select('api_order_details[bmtr_status_autoorder]', ['1' => 'Enabled', '0' => 'Disabled'], isset($api_order_details['bmtr_status_autoorder']) ? $api_order_details['bmtr_status_autoorder'] : '', ['class' => 'form-control', 'placeholder' => 'Select Auto Order Status']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @elseif(isset($supplier_name) && $supplier_name=='eden')
                                        <div class="col-lg-12">
                                            <div id="api_order_details">
                                            
                                            <div class="form-group" id="emailtext" style="display: none;">
                                              <label class="col-sm-2 control-label" for="input-csv_location">Email</label>
                                              <div class="col-sm-10">
                                                <input type="text" name="purchase_order_email" value="" placeholder="Enter Email" id="input-csv_location1" class="form-control">
                                              </div>
                                            </div>
                                            
                                            <div id="divcsv1" style="display: none;"></div>
                                                
                                            <div id="divftp1" style="display:none;">
                                                <legend>FTP Mode</legend>
                                              <fieldset>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-remote_file">Remote File</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="remote_file1" value="" placeholder="Enter Remote File" id="input-remote_file1" class="form-control">
                                                  </div>
                                                </div>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-local_file">Local File</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="local_file1" value="bvc" placeholder="Enter Local File" id="input-local_file1" class="form-control">
                                                  </div>
                                                </div>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-ftp_server">FTP Server</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="ftp_server1" value="" placeholder="Enter FTP Server" id="input-ftp_server1" class="form-control">
                                                  </div>
                                                </div>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-ftp_port">Enter FTP Port</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="ftp_port1" value="" placeholder="Enter FTP Port1" id="input-ftp_port1" class="form-control">
                                                  </div>
                                                </div>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-ftp_user">FTP User</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="ftp_user1" value="" placeholder="Enter FTP User" id="input-ftp_user1" class="form-control">
                                                  </div>
                                                </div>
                                                <div class="form-group required">
                                                  <label class="col-sm-2 control-label" for="input-ftp_password">FTP Password</label>
                                                  <div class="col-sm-10">
                                                    <input type="text" name="ftp_password1" value="" placeholder="Enter FTP Password" id="input-ftp_password1" class="form-control">
                                                  </div>
                                                </div>
                                              </fieldset>
                                            </div>
                                                
                                            <div id="divimportcsv1" style="">
                                              <fieldset>
                                                <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-import_condition">Upload Mode</label>
                                            <div class="col-sm-10">
                                                    <input type="radio" name="api_order_details[eden_upload_mode]" value="directory" {{ old('api_order_details.eden_upload_mode', $api_order_details['eden_upload_mode'] ?? '') == 'directory' ? 'checked' : '' }}>
                                                    Directory &nbsp; <input type="radio" name="api_order_details[eden_upload_mode]" value="ftp" {{ old('api_order_details.eden_upload_mode', $api_order_details['eden_upload_mode'] ?? '') == 'ftp' ? 'checked' : '' }}>
                                                    FTP
                                            </div>
                                        </div>
                                                <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">External Reference </label>
                                                        <div class="col-sm-10">
                                                          {{ Form::text('api_order_details[external_ref_append]', isset($api_order_details['external_ref_append']) ? $api_order_details['external_ref_append'] : '', ['class' => 'form-control','id'=>'input-external_ref_append', 'placeholder' => 'Enter extenal ref append sting']) }}
                                                          <span><small>(A string or number to append to jobid/orderid eg. Dils-001, Dils-002)</small></span>
                                                        </div>
                                                    </div>

                                                  <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">Item Type </label>
                                                        <div class="col-sm-10">
                                                          
                                                        {{ Form::select('api_order_details[item_type]', ['' => 'Select Type', 'tyres' => 'Tyres Only', 'all' => 'All'], old('api_order_details.item_type', $item_type ?? ''), ['class' => 'form-control', 'id' => 'input-item_type']) }}


                                                          <span><small>(Send tyres or services to eden)</small></span>
                                                        </div>
                                                    </div>
                                                
                                                <div id="div-directory" style="display: block;">
                                                    <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">Directory Path</label>
                                                        <div class="col-sm-10">
                                                          
                                                          {{ Form::text('api_order_details[eden_dir_path]', isset($api_order_details['eden_dir_path']) ? $api_order_details['eden_dir_path'] : '', ['class' => 'form-control','id'=>'input-eden_dir_path', 'placeholder' => 'Enter FTP Host']) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="div-ftp" style="display: none;">
                                                    <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">FTP Host</label>
                                                        <div class="col-sm-10">
                                                         
                                                          {{ Form::text('api_order_details[eden_ftp_host]', isset($api_order_details['eden_ftp_host']) ? $api_order_details['eden_ftp_host'] : '', ['class' => 'form-control','id'=>'input-eden_ftp_host', 'placeholder' => 'Enter FTP Host']) }}
                                                        </div>
                                                    </div>

                                                    <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">FTP User Name</label>
                                                        <div class="col-sm-10">
                                                         
                                                          {{ Form::text('api_order_details[eden_ftp_username]', isset($api_order_details['eden_ftp_username']) ? $api_order_details['eden_ftp_username'] : '', ['class' => 'form-control','id'=>'input-eden_ftp_username', 'placeholder' => 'Enter FTP Username']) }}
                                                        </div>
                                                    </div>

                                                    <div class="form-group required">
                                                        <label class="col-sm-2 control-label" for="input-eden_api_code">FTP Password</label>
                                                        <div class="col-sm-10">
                                                         
                                                          {{ Form::text('api_order_details[eden_ftp_password]', isset($api_order_details['eden_ftp_password']) ? $api_order_details['eden_ftp_password'] : '', ['class' => 'form-control','id'=>'input-eden_ftp_password', 'placeholder' => 'Enter FTP Password']) }}
                                                        </div>
                                                    </div>
                                                </div>
                                              
                                                <div class="form-group required">
                                                    <label class="col-sm-2 control-label" for="input-ftp_password">Enable Auto Orders</label>
                                                    <div class="col-sm-10">
                                                       <select name="api_order_details[eden_status_autoorder]" id="input-status_autoorder" class="form-control">
                                                           <option value="1">Enabled</option>
                                                            <option value="0">Disabled</option>
                                                        </select>
                                                    </div>
                                                </div>

                                              </fieldset>

                                            <script type="text/javascript">
                                                $(document).ready(function () {
                                                function toggleUploadMode() {
                                                var mode = $('input[name="api_order_details[eden_upload_mode]"]:checked').val();
                                                if (mode === 'directory') {
                                                $('#div-directory').show();
                                                $('#div-ftp').hide();
                                                } else if (mode === 'ftp') {
                                                $('#div-directory').hide();
                                                $('#div-ftp').show();
                                                } else {
                                                $('#div-directory').hide();
                                                $('#div-ftp').hide();
                                                }
                                                }
                                                $('input[name="api_order_details[eden_upload_mode]"]').on('click', toggleUploadMode);
                                                toggleUploadMode();
                                                });
                                            </script>

                                            </div>
                                            </div>
                                        </div>
                                        @endif()

                                    </div>
                                </div>
                                <!-- Submit and Import Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @if (isset($id) ? $id : '')
                            @include('AutoCare/supplier/leadtime')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const importMethodDropdown = document.getElementById('import_method');
            const csvUploadSection = document.getElementById('csv-upload');
            const ftpDetailsSection = document.getElementById('ftp-details');
            const filePathSection = document.getElementById('file-path');

            function toggleSections() {
                const selectedMethod = importMethodDropdown.value;
                csvUploadSection.style.display = selectedMethod === 'csv' ? 'block' : 'none';
                ftpDetailsSection.style.display = selectedMethod === 'ftp' ? 'block' : 'none';
                filePathSection.style.display = selectedMethod === 'file_path' ? 'block' : 'none';
            }

            toggleSections();

            importMethodDropdown.addEventListener('change', toggleSections);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tabContent = document.getElementById('ftp');
            var tabLink = document.getElementById('ftp-tab');
            document.getElementById('testConnectionButton').addEventListener('click', function () {
                var ftpHostElement = document.getElementById('ftp_host');
                var ftpUserElement = document.getElementById('ftp_user');
                var ftpPasswordElement = document.getElementById('ftp_password');

                if (!ftpHostElement || !ftpUserElement || !ftpPasswordElement) {
                    console.error('One or more form fields are missing!');
                    return;
                }

                var ftpHost = ftpHostElement.value;
                var ftpUser = ftpUserElement.value;
                var ftpPassword = ftpPasswordElement.value;

                // AJAX Request
                fetch('{{ route("ftp.test.connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ftp_host: ftpHost,
                        ftp_user: ftpUser,
                        ftp_password: ftpPassword
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        var connectionStatusMessage = document.getElementById('connectionStatusMessage');
                        if (data.success) {
                            connectionStatusMessage.style.color = 'green';
                            connectionStatusMessage.textContent = 'Connection successful! Directories loaded.';

                            var directorySelect = document.getElementById('ftp_directory');
                            directorySelect.innerHTML = ''; // Clear existing options
                            data.files.forEach(function (file) {
                                var option = document.createElement('option');
                                option.value = file;
                                option.textContent = file;
                                directorySelect.appendChild(option);
                            });
                        } else {
                            connectionStatusMessage.style.color = 'red';
                            connectionStatusMessage.textContent = 'Connection failed: ' + data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while testing the connection.');
                    });
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initially check the API Order Enable value and show/hide the API details section
            toggleApiOrderDetails();

            // Event listener to toggle the visibility when API Order Enable is changed
            document.getElementById('api_order_enable').addEventListener('change', toggleApiOrderDetails);
        });

        function toggleApiOrderDetails() {
            const apiOrderEnable = document.getElementById('api_order_enable').value;
            const apiOrderDetailsSection = document.getElementById('api_order_details');

            if (apiOrderEnable == '1') {
                apiOrderDetailsSection.style.display = 'block'; // Show API details
            } else {
                apiOrderDetailsSection.style.display = 'none'; // Hide API details
            }
        }
    </script>
@endsection